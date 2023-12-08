<?php

declare(strict_types=1);

namespace Growthapps\Gptsdk\ApiClient;

use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Growthapps\Gptsdk\Enum\PromptRunState;
use Growthapps\Gptsdk\Enum\Type;
use Growthapps\Gptsdk\Enum\VendorEnum;
use Growthapps\Gptsdk\Prompt;
use Growthapps\Gptsdk\PromptAttribute;
use Growthapps\Gptsdk\PromptMessage;
use Growthapps\Gptsdk\PromptParam;
use Growthapps\Gptsdk\PromptRun;
use Growthapps\Gptsdk\Request\GetPromptRequest;
use Symfony\Component\HttpClient\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use function array_column;
use function array_map;

class GptSdkApiClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        string $apiKey,
        string $version = 'v1'
    ) {
        $this->httpClient = $this->httpClient->withOptions(
            [
                'base_uri' => 'https://gpt-sdk.com/api/' . $version,
                'auth_bearer' => $apiKey,
            ]
        );
    }

    final public function getPrompts(GetPromptRequest $request): ArrayCollection
    {

        $result = $this->httpClient->request(
            'GET',
            '/prompts',
            [
                'json' => [
                    'key' => $request->promptKey,
                ],
            ],
        );

        if ($result->getStatusCode() !== 200) {
            throw new Exception(
                $result->getContent(false),
            );
        }

        return new ArrayCollection(array_map(
            fn (array $promptData) => new Prompt(
                promptKey: $promptData['key'],
                promptMessages: new ArrayCollection(array_map(
                    fn (array $message) => new PromptMessage(
                        role: $message['role'],
                        content: $message['content'],
                    ),
                    $promptData['prompt'] ?? [],
                )),
                attributes: new ArrayCollection(array_map(
                    fn (array $attribute) => new PromptAttribute(
                        key: $attribute['key'],
                        type: Type::tryFrom($attribute['type']),
                        value: $attribute['value'] ?? null,
                    ),
                    $promptData['attributes'] ?? [],
                )),
                params: new ArrayCollection(array_map(
                    fn (array $params) => new PromptParam(
                        key: $params['key'],
                        type: Type::tryFrom($params['type']),
                        nestedParams: new ArrayCollection(array_map(
                            fn (array $nestedParam) => new PromptParam(
                                key: $nestedParam['key'],
                                type: Type::tryFrom($nestedParam['type']),
                                value: $nestedParam['value'] ?? null,
                            ),
                            $params['nestedParams'] ?? [],
                        )),
                        nestedPrompt: $params['nestedPrompt'],
                    ),
                    $promptData['params'] ?? [],
                )),
                vendorKey: VendorEnum::tryFrom($promptData['connector']['vendor']),
                llmOptions: $promptData['llmOptions'] ?? [],
            ),
            $result->toArray()['data'] ?? [],
        ));
    }

    public function runPrompt(
        PromptRun $promptRun,
    ): PromptRun {
        $paramsArray = $promptRun->params ? array_column($promptRun->params->map(
            fn (PromptParam $promptParam) => [
                'key' => $promptParam->key,
                'value' => !empty($promptParam->nestedParams) ?
                    array_column($promptParam->nestedParams->map(
                        fn (PromptParam $promptParam) => [
                            'key' => $promptParam->key,
                            'value' => $promptParam->value,
                        ]
                    )->toArray(), 'key', 'value') :
                    $promptParam->value,
            ]
        )->toArray(), 'key', 'value') : [];

        $response = $this->httpClient->request(
            'POST',
            "/prompts/$promptRun->promptKey/run",
            [
                'json' => [
                    'paramValues' => $paramsArray,
                    'attributeValues' => $promptRun->attributes,
                ],
            ],
        );

        if ($response->getStatusCode() !== 200) {
            return $promptRun
                ->setError($response->getContent(false))
                ->setState(PromptRunState::FAILED);
        }

        $json = $response->toArray();
        if (!empty($json['result']['error'])) {
            return $promptRun
                ->setError($json['result']['error'])
                ->setState(PromptRunState::FAILED);
        }

        return $promptRun
            ->setResponse($json['result']['result'])
            ->setOutputCost($json['result']['outputCost'])
            ->setInputCost($json['result']['inputCost'])
            ->setState(PromptRunState::SUCCESS);
    }
}

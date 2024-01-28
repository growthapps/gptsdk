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
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function array_column;
use function array_map;
use function mb_strlen;

class GptSdkApiClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        string $apiKey,
        private string $version = 'v1',
    ) {
        $this->httpClient = $this->httpClient->withOptions(
            [
                'base_uri' => 'https://gpt-sdk.com/',
                'auth_bearer' => $apiKey,
            ],
        );
    }

    /**
     * @return ArrayCollection<array-key, Prompt>
     *
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
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

        /**
         * @var array<array-key, array<string, string>> $data
         */
        $data = $result->toArray()['data'];

        return new ArrayCollection(array_map(
            function (array $promptData): Prompt {
                /** @var array<array-key, array<string, string>> $promptMessages */
                $promptMessages = $promptData['prompt'];
                /** @var array<array-key, array<string, string>> $promptAttributes */
                $promptAttributes = (array) ($promptData['attributes'] ?? []);
                /** @var array<array-key, array<string, string>> $promptParams */
                $promptParams = (array) ($promptData['params'] ?? []);
                /** @var array<string, string> $promptConnector */
                $promptConnector = $promptData['connector'];
                /** @var array<string, string> $llmOptions */
                $llmOptions = $promptData['llmOptions'];

                return new Prompt(
                    promptKey: $promptData['key'],
                    promptMessages: new ArrayCollection(array_map(
                        fn (array $message) => new PromptMessage(
                            role: $message['role'],
                            content: $message['content'],
                        ),
                        $promptMessages,
                    )),
                    attributes: new ArrayCollection(array_map(
                        fn (array $attribute) => new PromptAttribute(
                            key: $attribute['key'],
                            type: Type::tryFrom($attribute['type']),
                            value: $attribute['value'],
                        ),
                        $promptAttributes,
                    )),
                    params: new ArrayCollection(array_map(
                        function (array $params) {
                            $type = Type::tryFrom($params['type']);
                            if ($type === Type::NESTED) {
                                /** @var array<array-key, array<string, string>> $nestedParams */
                                $nestedParams = (array) ($params['nestedParams'] ?? []);

                                return new PromptParam(
                                    key: $params['key'],
                                    type: $type,
                                    nestedParams: new ArrayCollection(array_map(
                                        fn (array $nestedParam) => new PromptParam(
                                            key: $nestedParam['key'],
                                            type: Type::tryFrom($nestedParam['type']),
                                        ),
                                        $nestedParams,
                                    )),
                                    nestedPrompt: $params['nestedPrompt'],
                                );
                            }

                            return new PromptParam(
                                key: $params['key'],
                                type: $type,
                            );
                        },
                        $promptParams,
                    )),
                    vendorKey: VendorEnum::tryFrom($promptConnector['vendor']),
                    llmOptions: new ArrayCollection($llmOptions),
                );
            },
            $data,
        ));
    }

    public function runPrompt(
        PromptRun $promptRun,
    ): PromptRun {
        $paramsArray = $promptRun->params ? array_column($promptRun->params->map(
            fn (PromptParam $promptParam) => [
                'key' => $promptParam->key,
                'value' => $promptParam->nestedParams !== null ?
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
            "/api/$this->version/prompts/$promptRun->promptKey/run",
            [
                'json' => [
                    'paramValues' => $paramsArray,
                    'attributeValues' => $promptRun->attributes->toArray(),
                ],
            ],
        );

        if ($response->getStatusCode() !== 200) {
            return $promptRun
                ->setError($response->getContent(false))
                ->setState(PromptRunState::FAILED);
        }

        $result = $response->toArray();
        if (isset($result['errorMessage']) && mb_strlen((string) $result['errorMessage']) > 0) {
            return $promptRun
                ->setError((string) ($result['errorMessage'] ?? ''))
                ->setState(PromptRunState::FAILED);
        }

        return $promptRun
            ->setResponse((string) ($result['output'] ?? ''))
            ->setOutputCost((int) ($result['outputCost'] ?? 0))
            ->setInputCost((int) ($result['inputCost'] ?? 0))
            ->setState(PromptRunState::SUCCESS);
    }

    public function getLogsCount(
        int $timestampFrom,
        int $timestampTo,
        ?string $promptKey = null,
        ?ArrayCollection $attributeValues = null
    ): int {
        $response = $this->httpClient->request(
            'GET',
            "/api/$this->version/logs/count",
            [
                'query' => [
                    'dateFrom' => $timestampFrom,
                    'dateTo' => $timestampTo,
                    'promptKey' => $promptKey,
                    'attributeValues' => $attributeValues?->toArray()
                ]
            ]
        );

        $result = $response->toArray();

        return (int) $result['count'] ?? 0;
    }
}

<?php

namespace Growthapps\Gptsdk\Vendor;

use Growthapps\Gptsdk\Enum\PromptRunState;
use Growthapps\Gptsdk\PromptRun;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AnthropicAiVendor implements VendorInterface
{
    private const DEFAULT_VERSION = '2023-06-01';

    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    public function execute(PromptRun $run): PromptRun
    {
        $compiledPrompt = $run->getCompiledPrompt();
        if (!$compiledPrompt) {
            return $run->setError(
                'Empty Prompt',
            )->setState(
                PromptRunState::FAILED,
            );
        }

        $response = $this->httpClient->request(
            'POST',
            'https://api.anthropic.com/v1/messages',
            [
                'headers' => [
                    'x-api-key' => $run->llmOptions['api_key'] ?? '',
                    'anthropic-version' => $run->llmOptions['version'] ?? self::DEFAULT_VERSION,
                    'content-type' => 'application/json'
                ],
                'json' =>
                    array_intersect_key(array_merge(
                        $run->llmOptions ?? [],
                        [
                            'messages' => $compiledPrompt->toArray(),
                        ],
                        [
                            'max_tokens' => 1000,
                        ],
                    ), array_flip([
                        'messages',
                        'max_tokens',
                        'model',
                        'top_k',
                        'temperature',
                        'top_p',
                        'stop_sequences',
                    ])),
            ],
        );

        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            return $run->setError(
                $response->getContent(false),
            )->setState(
                PromptRunState::FAILED,
            );
        }

        $json = $response->toArray();
        /** @var array<int, array<string, string>> $content */
        $content = $json['content'];
        /** @var array<string, int> $usage */
        $usage = $json['usage'];

        return $run->setResponse(
            $content[0]['text'] ?? '',
        )->setInputCost(
            $usage['input_tokens'] ?? 0,
        )->setOutputCost(
            $usage['output_tokens'] ?? 0,
        )->setState(
            PromptRunState::SUCCESS,
        );
    }
}

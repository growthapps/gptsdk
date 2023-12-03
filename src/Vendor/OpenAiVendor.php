<?php

/**
 * This file is part of gptsdk/gptsdk-library
 *
 * @copyright Copyright (c) AndriiMz <moroz97andrze@gmail.com>
 * @license https://opensource.org/license/mit/ MIT License
 */

declare(strict_types=1);

namespace Growthapps\Gptsdk\Vendor;

use Growthapps\Gptsdk\Enum\PromptRunState;
use Growthapps\Gptsdk\PromptRun;
use Symfony\Component\HttpClient\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use function array_filter;
use function array_merge;

class OpenAiVendor implements VendorInterface
{
    public function __construct(
        private HttpClientInterface $httpClient
    ) {}

    public function execute(PromptRun $run): PromptRun
    {
        $response = $this->httpClient->request(
            'POST',
            'https://api.openai.com/v1/chat/completions',
            [
                'auth_bearer' => $run->llmOptions['api_key'] ?? '',
                'json' =>
                    array_intersect_key(array_merge(
                        $run->llmOptions,
                        [
                            'messages' => $run->getCompiledPrompt()->toArray()
                        ],
                        [
                            'n' => 1,
                            'max_tokens' => 1000
                        ]
                    ), array_flip([
                        'messages',
                        'n',
                        'max_tokens',
                        'model',
                        'frequency_penalty',
                        'logit_bias',
                        'presence_penalty',
                        'response_format',
                        'seed',
                        'stop',
                        'temperature',
                        'top_p'
                    ]))
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

        return $run->setResponse(
            $json['choices'][0]['message']['content'] ?? '',
        )->setInputCost(
            $json['usage']['prompt_tokens'] ?? 0,
        )->setOutputCost(
            $json['usage']['completion_tokens'] ?? 0,
        )->setState(
            PromptRunState::SUCCESS,
        );
    }
}

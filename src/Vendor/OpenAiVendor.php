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
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function array_flip;
use function array_intersect_key;
use function array_merge;

class OpenAiVendor implements VendorInterface
{
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
            'https://api.openai.com/v1/chat/completions',
            [
                'auth_bearer' => $run->llmOptions['api_key'] ?? '',
                'json' =>
                    array_intersect_key(array_merge(
                        $run->llmOptions ?? [],
                        [
                            'messages' => $compiledPrompt->toArray(),
                        ],
                        [
                            'n' => 1,
                            'max_tokens' => 1000,
                        ],
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
                        'top_p',
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
        /** @var array<int, array<string, array<string, string>>> $choises */
        $choises = $json['choices'];
        /** @var array<string, int> $usage */
        $usage = $json['usage'];

        return $run->setResponse(
            $choises[0]['message']['content'] ?? '',
        )->setInputCost(
            $usage['prompt_tokens'] ?? 0,
        )->setOutputCost(
            $usage['completion_tokens'] ?? 0,
        )->setState(
            PromptRunState::SUCCESS,
        );
    }
}

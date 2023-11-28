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

class OpenAiVendor implements VendorInterface
{
    public function execute(PromptRun $run): PromptRun
    {
        $httpClient = HttpClient::create([]);
        $response = $httpClient->request(
            'post',
            'https://api.openai.com/v1/chat/completions',
            [
                'auth_bearer' => $run->llmOptions['apiKey'] ?? '',
                'json' => array_filter(array_merge(
                    [
                        'model' => $run->llmOptions['model'] ?? '',
                        'messages' => $run->getCompiledPrompt(),
                        'n' => 1,
                        'max_tokens' => $run->llmOptions['max_tokens'] ?? '',
                        'temperature' => $run->llmOptions['temperature'] ?? '',
                    ]
                ), fn($option) => $option)
            ]
        );

        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            return $run->setError(
                $response->getContent(false),
            )->setState(
                PromptRunState::FAILED
            );
        }

        $json = $response->toArray();

        return $run->setResponse(
            $json['choices'][0]['message']['content'] ?? ''
        )->setInputCost(
            $json['usage']['prompt_tokens'] ?? 0
        )->setOutputCost(
            $json['usage']['completion_tokens'] ?? 0
        )->setState(
            PromptRunState::SUCCESS
        );
    }
}

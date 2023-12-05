<?php

declare(strict_types=1);

namespace Growthapps\Test\Gptsdk;

use Growthapps\Gptsdk\ApiClient\GptSdkApiClient;
use Growthapps\Gptsdk\Prompt;
use Growthapps\Gptsdk\PromptMessage;
use Growthapps\Gptsdk\Request\GetPromptRequest;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class GptSdkApiClientTest extends TestCase
{
    public function testGetPrompts()
    {
        $mockHttpClient = $this->getMockBuilder(HttpClientInterface::class)->getMock();

        $mockResult = $this->getMockBuilder(ResponseInterface::class)->getMock();

        $mockResult->expects($this->once())->method('getStatusCode')->willReturn(200);

        $mockHttpClient->expects($this->once())->method('request')->willReturn($mockResult);

        $mockResult->expects($this->once())->method('toArray')->willReturn([
            'data' => [
                [
                    'key' => 'new-prompt',
                    'prompt' => [
                        [
                            'role' => 'user',
                            'content' => 'hello gpt.',
                        ]
                    ],
                    'attributes' => [
                        'key',
                        'type',
                        'value',
                    ],
                    'params' => [
                        'key',
                        'type',
                        'nestedPrompt',
                        'nestedParams' => [
                            'key',
                            'type',
                            'value',
                        ],
                    ],
                    'llmOptions',
                    'connector' => [
                        'vendor' ,
                    ],
                ],
            ],
        ]);
        $gptSdk = new GptSdkApiClient($mockHttpClient, 'abc');
        $prompts = $gptSdk->getPrompts(new GetPromptRequest('abc'));
        $newPrompt = $prompts->get(0);
        assert($newPrompt instanceof Prompt);
        $this->assertEquals($newPrompt, Prompt::class);
        $newPromptMessage = $newPrompt->promptMessages->get(0);
        assert($newPromptMessage instanceof PromptMessage);
        $this->assertEquals($newPromptMessage->content, 'hello gpt.');

    }
}

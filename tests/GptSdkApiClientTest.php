<?php

declare(strict_types=1);

namespace Growthapps\Test\Gptsdk;

use Growthapps\Gptsdk\ApiClient\GptSdkApiClient;
use Growthapps\Gptsdk\Prompt;
use Growthapps\Gptsdk\PromptMessage;
use Growthapps\Gptsdk\Request\GetPromptRequest;
use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class GptSdkApiClientTest extends TestCase
{
    public function testGetPrompts()
    {
        $requestMock = Mockery::mock(ResponseInterface::class, [
            'getStatusCode' => 200,
            'toArray' => [
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
            ]
        ]);
        $mockHttpClient = Mockery::mock(HttpClientInterface::class, [
            'request' => $requestMock
        ]);
        $mockHttpClient->shouldReceive('withOptions')->andReturn($mockHttpClient);


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

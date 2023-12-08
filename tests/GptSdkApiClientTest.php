<?php

declare(strict_types=1);

namespace Growthapps\Test\Gptsdk;

use Doctrine\Common\Collections\ArrayCollection;
use Growthapps\Gptsdk\ApiClient\GptSdkApiClient;
use Growthapps\Gptsdk\Enum\PromptRunState;
use Growthapps\Gptsdk\Enum\Type;
use Growthapps\Gptsdk\Enum\VendorEnum;
use Growthapps\Gptsdk\Prompt;
use Growthapps\Gptsdk\PromptMessage;
use Growthapps\Gptsdk\PromptParam;
use Growthapps\Gptsdk\PromptRun;
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
                           [
                                'key' => 'attrKey',
                                'type' => 'nested',
                                'value' => 'testattr',
                           ]
                        ],
                        'params' => [
                            [
                                'key' => 'paramKey',
                                'type' => 'nested',
                                'nestedPrompt' => 'new-prompt2',
                                'nestedParams' => [
                                    [
                                        'key' => 'new-nestedParam',
                                        'type' => 'nested',
                                        'value' => 'nestedValue',
                                        ]
                                ],
                            ]
                        ],
                        'llmOptions' => ["a", "b", "c"],
                        'connector' => [
                            'vendor' => 'openAi' ,
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
        $prompts = $gptSdk->getPrompts(new GetPromptRequest('new-prompt'));
        $newPrompt = $prompts->get(0);
        assert($newPrompt instanceof Prompt);
        $this->assertEquals(get_class($newPrompt), Prompt::class);
        $newPromptMessage = $newPrompt->promptMessages->get(0);
        assert($newPromptMessage instanceof PromptMessage);
        $this->assertEquals($newPromptMessage->content, 'hello gpt.');
    }

    public function testRunPromptBadRequest()
    {
        //status code != 200
        $requestMock = Mockery::mock(ResponseInterface::class, [
            'getStatusCode' => 400,
            'toArray' => [
                'result' => [
                    'result' => 'success',
                    'error' => 'failRequest',
                    'inputCost' => 100,
                    'outputCost' => 200
                ]
            ],
            'getContent' => 'newError'
        ]);

        $mockHttpClient = Mockery::mock(HttpClientInterface::class, [
            'request' => $requestMock
        ]);
        $mockHttpClient->shouldReceive('withOptions')->andReturn($mockHttpClient);
        $promptRun = new PromptRun(
            vendorKey: VendorEnum::OPENAI,
            promptMessages: new ArrayCollection(
                [
                    new PromptMessage(
                        role: 'User',
                        content: 'Hello gpt! How are you? Reply in [[tone]] tone.',
                    ),
                ],
            ),
            promptKey: 'hello_prompt',
            params: new ArrayCollection(
                [
                    new PromptParam(
                        type: Type::STRING,
                        key: 'tone',
                        value: 'angry',
                    ),
                ],
            ),
        );

        $gptSdk = new GptSdkApiClient($mockHttpClient, 'abc');
        $promptRunResult = $gptSdk->runPrompt($promptRun);

        $this->assertEquals(PromptRunState::FAILED, $promptRunResult->getState());
        $this->assertEquals('newError', $promptRunResult->getError());

    }
    public function testRunPromptApiError()
    {
        //!empty($json['result']['error'])
        $requestMock = Mockery::mock(ResponseInterface::class, [
            'getStatusCode' => 200,
            'toArray' => [
                'result' => [
                    'result' => 'success',
                    'error' => 'failRequest',
                    'inputCost' => 100,
                    'outputCost' => 200
                ]
            ],
            'getContent' => 'newError'
        ]);

        $mockHttpClient = Mockery::mock(HttpClientInterface::class, [
            'request' => $requestMock
        ]);
        $mockHttpClient->shouldReceive('withOptions')->andReturn($mockHttpClient);
        $promptRun = new PromptRun(
            vendorKey: VendorEnum::OPENAI,
            promptMessages: new ArrayCollection(
                [
                    new PromptMessage(
                        role: 'User',
                        content: 'Hello gpt! How are you? Reply in [[tone]] tone.',
                    ),
                ],
            ),
            promptKey: 'hello_prompt',
            params: new ArrayCollection(
                [
                    new PromptParam(
                        type: Type::STRING,
                        key: 'tone',
                        value: 'angry',
                    ),
                ],
            ),
        );

        $gptSdk = new GptSdkApiClient($mockHttpClient, 'abc');
        $promtRunResult = $gptSdk->runPrompt($promptRun);

        $this->assertEquals(PromptRunState::FAILED, $promtRunResult->getState());
        $this->assertEquals('failRequest', $promtRunResult->getError());
    }

    public function testRunPrompt()
    {
        // result result

        $requestMock = Mockery::mock(ResponseInterface::class, [
            'getStatusCode' => 200,
            'toArray' => [
                'result' => [
                    'result' => 'success',
                    'error' => '',
                    'inputCost' => 100,
                    'outputCost' => 200
                ]
            ],
            'getContent' => 'newError'
        ]);

        $mockHttpClient = Mockery::mock(HttpClientInterface::class, [
            'request' => $requestMock
        ]);
        $mockHttpClient->shouldReceive('withOptions')->andReturn($mockHttpClient);
        $promptRun = new PromptRun(
            vendorKey: VendorEnum::OPENAI,
            promptMessages: new ArrayCollection(
                [
                    new PromptMessage(
                        role: 'User',
                        content: 'Hello gpt! How are you? Reply in [[tone]] tone.',
                    ),
                ],
            ),
            promptKey: 'hello_prompt',
            params: new ArrayCollection(
                [
                    new PromptParam(
                        type: Type::STRING,
                        key: 'tone',
                        value: 'angry',
                    ),
                ],
            ),
        );

        $gptSdk = new GptSdkApiClient($mockHttpClient, 'abc');
        $promptRunResult = $gptSdk->runPrompt($promptRun);
        $this->assertEquals('success', $promptRunResult->getResponse());
        $this->assertEquals(100, $promptRunResult->getOutputCost());
        $this->assertEquals(200, $promptRunResult->getInputCost());
        $this->assertEquals(PromptRunState::SUCCESS, $promptRunResult->getState());
    }
}

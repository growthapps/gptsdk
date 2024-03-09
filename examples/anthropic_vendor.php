<?php

use Doctrine\Common\Collections\ArrayCollection;

use Growthapps\Gptsdk\Vendor\AnthropicAiVendor;
use Growthapps\Gptsdk\PromptRun;
use Growthapps\Gptsdk\PromptMessage;

use Symfony\Component\HttpClient\HttpClient;

require_once __DIR__ . '/../vendor/autoload.php';


$anthropicAiVendor = new AnthropicAiVendor(HttpClient::create());

$promptRun = $anthropicAiVendor->execute(
    (new PromptRun(
        promptMessages: new ArrayCollection([
            new PromptMessage(
                role: 'user',
                content: 'hello world!'
            )
        ]),
        llmOptions: [
            'api_key' => 'sk-ant-api03-EmdFzZ_lTZs3qx6P6LC1FBgk9ONxYl',
            'model' => 'claude-3-opus-20240229'
        ]
    ))
);

echo $promptRun->getResponse() . PHP_EOL;
echo $promptRun->getError() . PHP_EOL;


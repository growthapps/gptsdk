<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Doctrine\Common\Collections\ArrayCollection;
use Growthapps\Gptsdk\ApiClient\GptSdkApiClient;
use Growthapps\Gptsdk\Enum\{ VendorEnum, Type };
use Growthapps\Gptsdk\{ PromptRun, PromptMessage, PromptParam };

$gptSdkClient = new GptSdkApiClient(
    'myapikey'
);
$promptRun = $gptSdkClient->runPrompt(
    new PromptRun(
        vendorKey: VendorEnum::OPENAI,
        promptMessages: new ArrayCollection(
            [
                new PromptMessage(
                    role: 'User',
                    content: 'Hello gpt! How are you? Reply in [[tone]] tone.'
                )
            ]
        ),
        promptKey: 'hello_prompt',
        params: new ArrayCollection(
            [
                new PromptParam(
                    type: Type::STRING,
                    key: 'tone',
                    value: 'angry'
                )
            ]
        ),
    )
);


echo $promptRun->getResponse();

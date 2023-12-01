<?php

declare(strict_types=1);

namespace Growthapps\Gptsdk\Runner;

use Growthapps\Gptsdk\ApiClient\GptSdkApiClient;
use Growthapps\Gptsdk\PromptRun;

class PromptApiRunner implements PromptRunnerInterface
{
    private GptSdkApiClient $gptSdkApiClient;

    public function __construct(GptSdkApiClient $gptSdkApiClient)
    {
        $this->gptSdkApiClient = $gptSdkApiClient;
    }

    final public function run(PromptRun $promptRun): PromptRun
    {
        return $this->gptSdkApiClient->runPrompt(
            $promptRun,
        );
    }
}

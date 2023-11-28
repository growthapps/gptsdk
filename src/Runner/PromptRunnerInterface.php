<?php

namespace Growthapps\Gptsdk\Runner;

use Growthapps\Gptsdk\PromptRun;

interface PromptRunnerInterface
{
    public function run(PromptRun $promptRun): PromptRun;
}

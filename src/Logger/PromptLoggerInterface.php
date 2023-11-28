<?php

namespace Growthapps\Gptsdk\Logger;

use Growthapps\Gptsdk\PromptRun;

interface PromptLoggerInterface
{
    public function log(PromptRun $promptRun): PromptRun;
}

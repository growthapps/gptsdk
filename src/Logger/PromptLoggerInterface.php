<?php

declare(strict_types=1);

namespace Growthapps\Gptsdk\Logger;

use Growthapps\Gptsdk\PromptRun;

interface PromptLoggerInterface
{
    public function log(PromptRun $promptRun): PromptRun;
}

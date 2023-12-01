<?php

declare(strict_types=1);

namespace Growthapps\Gptsdk\Runner;

use Doctrine\Common\Collections\ArrayCollection;
use Growthapps\Gptsdk\Compiler\PromptCompilerInterface;
use Growthapps\Gptsdk\Enum\PromptRunState;
use Growthapps\Gptsdk\Logger\PromptLoggerInterface;
use Growthapps\Gptsdk\PromptRun;
use Growthapps\Gptsdk\Vendor\VendorInterface;
use Throwable;

use function assert;

class PromptLocalRunner implements PromptRunnerInterface
{
    public function __construct(
        private PromptCompilerInterface $promptCompiler,
        /** @var ArrayCollection<VendorInterface> $vendors */
        private ArrayCollection $vendors,
        private ?PromptLoggerInterface $promptLogger = null,
    ) {
    }

    final public function run(PromptRun $promptRun): PromptRun
    {
        $promptRun = $this->promptCompiler->compile($promptRun);
        $vendor = $this->vendors->get($promptRun->vendorKey->value);
        try {
            if ($vendor) {
                assert($vendor instanceof VendorInterface);
                $promptRun = $vendor->execute($promptRun);
            }
        } catch (Throwable $e) {
            $promptRun = $promptRun
                ->setError($e->getMessage())
                ->setState(PromptRunState::FAILED);
        }

        if ($this->promptLogger) {
            $this->promptLogger->log($promptRun);
        }

        return $promptRun;
    }
}

<?php
/**
 * This file is part of gptsdk/gptsdk-library
 *
 * @copyright Copyright (c) AndriiMz <moroz97andrze@gmail.com>
 * @license https://opensource.org/license/mit/ MIT License
 */
declare(strict_types=1);
namespace Growthapps\Gptsdk;

use Doctrine\Common\Collections\ArrayCollection;
use Growthapps\Gptsdk\Enum\PromptRunState;
use Growthapps\Gptsdk\Enum\VendorEnum;

/**
 * @property PromptParam[] $params
 */
class PromptRun
{
    private ?ArrayCollection $compiledPrompt = null;

    private ?string $response;
    private ?string $error;
    private ?int $inputCost;
    private ?int $outputCost;


    private PromptRunState $state = PromptRunState::CREATED;


    public function __construct(
        public readonly VendorEnum $vendorKey,
        public readonly ArrayCollection $promptMessages,
        public readonly string $promptKey,
        public readonly ?array $llmOptions = null,
        public readonly ?ArrayCollection $params = null,
        public readonly ?array $attributes = null,
        public readonly ?array $payload = null,
        public readonly string $paramOpenTag = '[[',
        public readonly string $paramCloseTag = ']]',
    ) {}

    public function getCompiledPrompt(): ?ArrayCollection
    {
        return $this->compiledPrompt;
    }

    public function setCompiledPrompt(ArrayCollection $compiledPrompt): PromptRun
    {
        $this->compiledPrompt = $compiledPrompt;
        return $this;
    }

    public function getState(): PromptRunState
    {
        return $this->state;
    }

    public function setState(PromptRunState $state): PromptRun
    {
        $this->state = $state;
        return $this;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(?string $response): PromptRun
    {
        $this->response = $response;
        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): PromptRun
    {
        $this->error = $error;
        return $this;
    }

    public function getInputCost(): ?int
    {
        return $this->inputCost;
    }

    public function setInputCost(?int $inputCost): PromptRun
    {
        $this->inputCost = $inputCost;
        return $this;
    }

    public function getOutputCost(): ?int
    {
        return $this->outputCost;
    }

    public function setOutputCost(?int $outputCost): PromptRun
    {
        $this->outputCost = $outputCost;
        return $this;
    }
}

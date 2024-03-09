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

use function microtime;

class PromptRun
{
    /**
     * @var ArrayCollection<array-key, PromptMessage>|null
     */
    private ?ArrayCollection $compiledPrompt = null;
    private ?string $response = null;
    private ?string $error = null;
    private ?int $inputCost = null;
    private ?int $outputCost = null;
    private float $startedAtMs;


    private PromptRunState $state = PromptRunState::CREATED;

    //TODO: split PromptRun to PromptVendorRun and PromptApiRun
    /**
     * @param ArrayCollection<array-key, PromptMessage> $promptMessages
     * @param array<string, mixed>|null $llmOptions
     * @param ArrayCollection<array-key, PromptParam>|null $params
     * @param ArrayCollection<array-key, PromptAttribute>|null $attributes
     * @param ArrayCollection<string, mixed>|null $payload
     */
    public function __construct(
        public readonly ?VendorEnum $vendorKey = null,
        public readonly ?ArrayCollection $promptMessages = null,
        public readonly ?string $promptKey = null,
        public readonly ?array $llmOptions = null,
        public readonly ?ArrayCollection $params = null,
        public readonly ?ArrayCollection $attributes = null,
        public readonly ?ArrayCollection $payload = null,
        public readonly string $paramOpenTag = '[[',
        public readonly string $paramCloseTag = ']]',
    ) {
        $this->startedAtMs = microtime(true);
    }

    /**
     * @return ArrayCollection<array-key, PromptMessage>|null
     */
    public function getCompiledPrompt(): ?ArrayCollection
    {
        return $this->compiledPrompt ?? $this->promptMessages;
    }

    /**
     * @param ArrayCollection<array-key, PromptMessage> $compiledPrompt
     *
     * @return $this
     */
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

    public function getDurationMs(): float
    {
        return microtime(true) - $this->startedAtMs;
    }
}

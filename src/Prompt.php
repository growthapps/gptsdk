<?php

declare(strict_types=1);

namespace Growthapps\Gptsdk;

use Doctrine\Common\Collections\ArrayCollection;
use Growthapps\Gptsdk\Enum\VendorEnum;

class Prompt
{
    /**
     * @param ArrayCollection<array-key, PromptMessage>|null $promptMessages
     * @param ArrayCollection<array-key, PromptAttribute>|null $attributes
     * @param ArrayCollection<array-key, PromptParam>|null $params
     * @param ArrayCollection<string, string>|null $llmOptions
     *
     * @psalm-suppress DocblockTypeContradiction
     */
    public function __construct(
        public string $promptKey,
        public ?ArrayCollection $promptMessages,
        public ?ArrayCollection $attributes,
        public ?ArrayCollection $params,
        public ?VendorEnum $vendorKey,
        public readonly ?ArrayCollection $llmOptions = null,
    ) {
    }
}

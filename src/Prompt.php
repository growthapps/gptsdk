<?php

declare(strict_types=1);

namespace Growthapps\Gptsdk;

use Doctrine\Common\Collections\ArrayCollection;

class Prompt
{
    public function __construct(
        public string $promptKey,
        public ?ArrayCollection $promptMessages,
        public ?ArrayCollection $attributes,
        public ?ArrayCollection $params,
        public string $vendorKey,
        public readonly ?array $llmOptions = null,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace Growthapps\Gptsdk;

use Growthapps\Gptsdk\Enum\Type;

class PromptAttribute
{
    public function __construct(
        public readonly string $key,
        public readonly Type $type,
        public readonly ?string $value = null,
    ) {
    }
}

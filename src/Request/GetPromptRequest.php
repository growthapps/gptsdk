<?php

declare(strict_types=1);

namespace Growthapps\Gptsdk\Request;

class GetPromptRequest
{
    public function __construct(
        public ?string $promptKey,
    ) {
    }
}

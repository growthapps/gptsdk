<?php

namespace Growthapps\Gptsdk\Request;

class GetPromptRequest
{
    public function __construct(
        public ?string $promptKey
    ){}
}

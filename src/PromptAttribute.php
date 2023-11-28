<?php

namespace Growthapps\Gptsdk;

use Doctrine\Common\Collections\ArrayCollection;
use Growthapps\Gptsdk\Enum\Type;

class PromptAttribute
{
    public function __construct(
        public readonly string           $key,
        public readonly Type             $type,
        public readonly ?string          $value = null
    ) {}
}

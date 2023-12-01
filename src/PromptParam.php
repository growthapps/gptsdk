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
use Growthapps\Gptsdk\Enum\Type;

class PromptParam
{
    public function __construct(
        public readonly string $key,
        public readonly Type $type,
        public readonly ?string $value = null,
        public readonly ?string $defaultValue = null,
        public readonly ?ArrayCollection $nestedParams = null,
        public readonly ?string $nestedPrompt = null,
    ) {
    }
}

<?php

/**
 * This file is part of gptsdk/gptsdk-library
 *
 * @copyright Copyright (c) AndriiMz <moroz97andrze@gmail.com>
 * @license https://opensource.org/license/mit/ MIT License
 */

declare(strict_types=1);

namespace Growthapps\Gptsdk;

use JsonSerializable;

class PromptMessage implements JsonSerializable
{
    public function __construct(
        public string $role,
        public string $content,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'role' => $this->role,
            'content' => $this->content,
        ];
    }
}

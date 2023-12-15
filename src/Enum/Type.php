<?php

/**
 * This file is part of gptsdk/gptsdk-library
 *
 * @copyright Copyright (c) AndriiMz <moroz97andrze@gmail.com>
 * @license https://opensource.org/license/mit/ MIT License
 */

declare(strict_types=1);

namespace Growthapps\Gptsdk\Enum;

enum Type: string
{
    case NESTED = 'nested';
    case STRING = 'string';
    case ENUM = 'enum';
    case NUMBER = 'number';
    case TEXT = 'text';
}

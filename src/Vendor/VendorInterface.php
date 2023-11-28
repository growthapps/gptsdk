<?php
/**
 * This file is part of gptsdk/gptsdk-library
 *
 * @copyright Copyright (c) AndriiMz <moroz97andrze@gmail.com>
 * @license https://opensource.org/license/mit/ MIT License
 */
declare(strict_types=1);
namespace Growthapps\Gptsdk\Vendor;

use Growthapps\Gptsdk\PromptRun;

interface VendorInterface
{
    public function execute(PromptRun $run): PromptRun;

}

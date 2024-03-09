<?php

/**
 * This file is part of gptsdk/gptsdk-library
 *
 * @copyright Copyright (c) AndriiMz <moroz97andrze@gmail.com>
 * @license https://opensource.org/license/mit/ MIT License
 */

declare(strict_types=1);

namespace Growthapps\Gptsdk\Compiler;

use Doctrine\Common\Collections\ArrayCollection;
use Growthapps\Gptsdk\Enum\PromptRunState;
use Growthapps\Gptsdk\Enum\Type;
use Growthapps\Gptsdk\PromptMessage;
use Growthapps\Gptsdk\PromptRun;

use function array_keys;
use function array_values;
use function str_replace;

class PromptCompiler implements PromptCompilerInterface
{
    final public function compile(PromptRun $promptRun): PromptRun
    {
        $paramKeyValue = $promptRun->params;
        $paramKeyValueReplacements = [];

        $wrap = fn (string $what): string => "$promptRun->paramOpenTag$what$promptRun->paramCloseTag";

        foreach ($paramKeyValue ?? [] as $param) {
            if ($param->type === Type::NESTED) {
                if ($param->value === null || $param->value === '') {
                    $paramKeyValueReplacements[$wrap($param->key)] = '';
                } else {
                    $nestedParamKeyValue = [];
                    foreach ($param->nestedParams ?? [] as $nestedParam) {
                        $nestedParamKeyValue[$wrap($nestedParam->key)] = $nestedParam->value ?? '';
                    }

                    if ($param->nestedPrompt !== null) {
                        $paramKeyValueReplacements[$wrap($param->key)] = str_replace(
                            array_keys($nestedParamKeyValue),
                            array_values($nestedParamKeyValue),
                            $param->nestedPrompt,
                        );
                    }
                }

                continue;
            }

            $paramKeyValueReplacements[$wrap($param->key)] = $param->value ?? '';
        }

        /** @var ArrayCollection<array-key, PromptMessage> $compiledPrompt */
        $compiledPrompt = new ArrayCollection();
        foreach ($promptRun->promptMessages?->getValues() ?? [] as $key => $message) {
            $compiledPrompt->set($key, new PromptMessage(
                role: $message->role,
                content: str_replace(
                    array_keys($paramKeyValueReplacements),
                    array_values($paramKeyValueReplacements),
                    $message->content,
                ),
            ));
        }

        return $promptRun->setCompiledPrompt(
            $compiledPrompt,
        )->setState(
            PromptRunState::COMPILED,
        );
    }
}

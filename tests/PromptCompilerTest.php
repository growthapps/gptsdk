<?php

declare(strict_types=1);

namespace Growthapps\Test\Gptsdk;

use Doctrine\Common\Collections\ArrayCollection;
use Growthapps\Gptsdk\Compiler\PromptCompiler;
use Growthapps\Gptsdk\Enum\PromptRunState;
use Growthapps\Gptsdk\Enum\Type;
use Growthapps\Gptsdk\Enum\VendorEnum;
use Growthapps\Gptsdk\PromptMessage;
use Growthapps\Gptsdk\PromptParam;
use Growthapps\Gptsdk\PromptRun;
use PHPUnit\Framework\TestCase;

use function assert;

class PromptCompilerTest extends TestCase
{
    private PromptCompiler $promptCompiler;

    protected function setUp(): void
    {
        $this->promptCompiler = new PromptCompiler();
    }

    public function testCompile(): void
    {
        $promptRun = new PromptRun(
            vendorKey: VendorEnum::OPENAI,
            promptMessages: new ArrayCollection(
                [
                    new PromptMessage(
                        role: 'User',
                        content: 'Hello gpt! How are you? Reply in [[tone]] tone.',
                    ),
                ],
            ),
            promptKey: 'hello_prompt',
            params: new ArrayCollection(
                [
                    new PromptParam(
                        type: Type::STRING,
                        key: 'tone',
                        value: 'angry',
                    ),
                ],
            ),
        );

        $promptRun = $this->promptCompiler->compile($promptRun);
        $compiledPrompt = $promptRun->getCompiledPrompt();
        $this->assertNotNull($compiledPrompt);
        $this->assertCount(1, $compiledPrompt);
        $this->assertEquals(PromptRunState::COMPILED, $promptRun->getState());
        $compiledMessage = $compiledPrompt->get(0);
        assert($compiledMessage instanceof PromptMessage);
        $this->assertEquals('Hello gpt! How are you? Reply in angry tone.', $compiledMessage->content);
    }
}

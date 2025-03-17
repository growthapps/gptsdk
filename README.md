### ⚠️ Repository Deprecated ⚠️  

This repository has been **abandoned** and is no longer maintained.  

Please use the new repository: **[saassdk/gptsdk-php](https://github.com/saassdk/gptsdk-php)** for the latest updates and improvements.  

We recommend switching to the new version to ensure compatibility, security, and access to new features.  

Thank you for your support! 🚀  


<h1 align="center">growthapps/gptsdk</h1>


<p align="center">
    <strong>Develop ai features in your application 10x faster</strong>
</p>

<!--
TODO: Make sure the following URLs are correct and working for your project.
      Then, remove these comments to display the badges, giving users a quick
      overview of your package.

<p align="center">
    <a href="https://github.com/growthapps/gptsdk-library"><img src="https://img.shields.io/badge/source-gptsdk/gptsdk--library-blue.svg?style=flat-square" alt="Source Code"></a>
    <a href="https://packagist.org/packages/gptsdk/gptsdk-library"><img src="https://img.shields.io/packagist/v/gptsdk/gptsdk-library.svg?style=flat-square&label=release" alt="Download Package"></a>
    <a href="https://php.net"><img src="https://img.shields.io/packagist/php-v/gptsdk/gptsdk-library.svg?style=flat-square&colorB=%238892BF" alt="PHP Programming Language"></a>
    <a href="https://github.com/growthapps/gptsdk-library/blob/main/LICENSE"><img src="https://img.shields.io/packagist/l/gptsdk/gptsdk-library.svg?style=flat-square&colorB=darkcyan" alt="Read License"></a>
    <a href="https://github.com/growthapps/gptsdk-library/actions/workflows/continuous-integration.yml"><img src="https://img.shields.io/github/actions/workflow/status/growthapps/gptsdk-library/continuous-integration.yml?branch=main&style=flat-square&logo=github" alt="Build Status"></a>
    <a href="https://codecov.io/gh/growthapps/gptsdk-library"><img src="https://img.shields.io/codecov/c/gh/growthapps/gptsdk-library?label=codecov&logo=codecov&style=flat-square" alt="Codecov Code Coverage"></a>
    <a href="https://shepherd.dev/github/growthapps/gptsdk-library"><img src="https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fshepherd.dev%2Fgithub%2Fgrowthapps%2Fgptsdk-library%2Fcoverage" alt="Psalm Type Coverage"></a>
</p>
-->


With GptSdk, we can overcome problems with AI management and focus on the growth of our application. Make AI the ‘detail’ of your application, not the ‘business rule.’ GptSdk allows software development teams to take AI feature development to a new level.

Use this library to add AI to your Laravel/Symfony application.
Use [GptSdk](https://gpt-sdk.com?via=github) to overcome prompt management problems.

## 📲 Installation

Install this package as a dependency using [Composer](https://getcomposer.org).

``` bash
composer require growthapps/gptsdk
```


## 🎢 Usage
You can use this library without GptSdk account.
Just install this package into your laravel/symfony application and enjoy openai integration.
``` php
$promptLocalRunner = new PromptLocalRunner(
    new PromptCompiler(),
    new ArrayCollection([
        VendorEnum::OPENAI->value => new OpenAiVendor(
            HttpClient::create()
        )
    ],
    new PromptRunLogger()
);

$promptRun = $promptLocalRunner->run(
    new PromptRun(
        vendorKey: VendorEnum::OPENAI,
        promptMessages: new ArrayCollection(
            [
                new PromptMessage(
                    role: 'User',
                    content: 'Hello gpt! How are you? Reply in [[tone]] tone.'
                )
            ]
        ),
        promptKey: 'hello_prompt',
        params: new ArrayCollection(
            [
                new PromptParam(
                    type: Type::STRING,
                    key: 'tone',
                    value: 'angry'
                )
            ]
        ),
    )
);
```

If you already created GptSdk account you can use `GptSdkApiClient`:
- create prompt in the GptSdk (https://gpt-sdk.com?via=github)
- set prompt key under API section on the right
- copy prompt key, api key and use `GptSdkApiClient` to send prompt in your code:
```php
$gptSdkClient = new GptSdkApiClient(
    HttpClient::create(),
    'myapikey'
);
$promptRun = $gptSdkClient->runPrompt(
    new PromptRun(
        promptKey: 'hello_prompt',
        params: new ArrayCollection(
            [
                new PromptParam(
                    type: Type::STRING,
                    key: 'tone',
                    value: 'angry'
                )
            ]
        ),
    )
);

echo $promptRun->getResponse();
```

## 🧠 Ai Vendors
### OpenAi
The GptSdk PHP library serves as a powerful bridge between PHP applications and OpenAI's completion API, seamlessly integrating the two for enhanced language processing capabilities. By incorporating the GptSdk library into PHP-based projects, developers can effortlessly tap into OpenAI's state-of-the-art language models to achieve advanced natural language generation and comprehension. This integration enables PHP applications to leverage the sophisticated capabilities of OpenAI, automating complex language-related tasks, generating creative content, and enhancing overall user engagement. The GptSdk library streamlines the integration process, offering a user-friendly interface that facilitates efficient communication with OpenAI's completion API. With GptSdk, PHP developers can harness the full potential of OpenAI's language models, empowering them to create dynamic, intelligent applications that elevate the quality and responsiveness of their projects.

### Anthropic
The GptSdk PHP library seamlessly connects PHP applications with the Anthropic API, unlocking advanced AI capabilities. With GptSdk, PHP developers can easily integrate and leverage Anthropic's powerful features, enabling intelligent data analysis, pattern recognition, and streamlined decision-making. The user-friendly interface simplifies the integration process, empowering developers to enhance their PHP projects with Anthropic's cutting-edge AI functionalities. GptSdk facilitates a new era of adaptive PHP applications, redefining the landscape of AI-driven solutions through effortless interaction with the Anthropic API.

## 🏯 Architecuture
### Runners
#### PromptApiRunner
Sends a request to gpt-sdk.com

#### PromptLocalRunner
Sends a request directly to llm.


### Webhooks (TBD)

## 📰 Articles
#### [How to create AI tools 10x faster](https://medium.com/@moroz97andrze/unleashing-ai-potential-overcoming-prompt-management-hurdles-with-gptsdk-43f067681fa1)

## Contributing

Contributions are welcome! To contribute, please familiarize yourself with
[CONTRIBUTING.md](CONTRIBUTING.md).

## Coordinated Disclosure

Keeping user information safe and secure is a top priority, and we welcome the
contribution of external security researchers. If you believe you've found a
security issue in software that is maintained in this repository, please read
[SECURITY.md](SECURITY.md) for instructions on submitting a vulnerability report.






## Copyright and License

gptsdk/gptsdk-library is copyright © [AndriiMz](https://gpt-sdk.com)
and licensed for use under the terms of the
MIT License (MIT). Please see [LICENSE](LICENSE) for more information.



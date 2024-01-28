<?php


use Growthapps\Gptsdk\ApiClient\GptSdkApiClient;
use Symfony\Component\HttpClient\HttpClient;

$gptSdkClient = new GptSdkApiClient(
    HttpClient::create(),
    'yourapikey'
);
$promptRun = $gptSdkClient->getLogsCount(
    1674909374,
    1716445374,
    'news-summarizer',
    new \Doctrine\Common\Collections\ArrayCollection([
        ['key' => 'accountId', 'value' => 1]
    ])
);

echo $promptRun . PHP_EOL;

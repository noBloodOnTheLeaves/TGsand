<?php

namespace App\Http\Controllers\API;

use GuzzleHttp\Client;

class YandexTranslate extends \App\Http\Controllers\Controller
{
    protected Client $ya;
    protected array $headers;
    protected string $targetLanguage;
    protected array $postBody;
    protected array $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.yandex.translate_api_url');

        $this->headers = [
            'Authorization' => 'Bearer ' . config('services.yandex.token'),
            'Accept'        => 'application/json',
        ];

        $this->targetLanguage = 'en';

        $this->postBody = [
            'targetLanguageCode' => $this->targetLanguage,
            'texts' => '',
            'folderId' => config('services.yandex.cloud_folder'),
        ];

        $this->ya = new Client([
            'base_uri' => $this->apiUrl,
            'headers' => $this->headers,
        ]);
    }

    public function translate(string $text): string
    {
        $this->postBody['texts'] = $text;

        $response = $this->ya->post('translate', [
            'json' => $this->postBody,
        ]);

        $response = json_decode($response->getBody()->getContents(), true);

        return $response['translations'][0]['text'];
    }
}

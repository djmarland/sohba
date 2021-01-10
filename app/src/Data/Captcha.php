<?php
declare(strict_types=1);

namespace App\Data;

use GuzzleHttp\Client;
use function http_build_query;
use function json_decode;

class Captcha
{
    private const URI = 'https://www.google.com/recaptcha/api/siteverify';

    private string $appConfigCaptchaKey;
    private Client $httpClient;

    public function __construct(
        Client $httpClient,
        string $appConfigCaptchaKey
    ) {
        $this->appConfigCaptchaKey = $appConfigCaptchaKey;
        $this->httpClient = $httpClient;
    }

    public function isValid(string $guess): bool
    {
        $params = [
            'secret' => $this->appConfigCaptchaKey,
            'response' => $guess,
        ];

        $fullUrl = self::URI . '?' . http_build_query($params);

        $verifyResponse = $this->httpClient->request('GET', $fullUrl);

        $responseData = json_decode($verifyResponse->getBody()->getContents());

        return !!$responseData->success;
    }
}

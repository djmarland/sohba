<?php
declare(strict_types=1);

namespace App\Data;

use GuzzleHttp\Client;

class Captcha
{
    private const URI = 'https://www.google.com/recaptcha/api/siteverify';

    private $appConfigCaptchaKey;
    private $httpClient;

    public function __construct(
        Client $httpClient,
        string $appConfigCaptchaKey
    ) {
        $this->appConfigCaptchaKey = $appConfigCaptchaKey;
        $this->httpClient = $httpClient;
    }

    public function isValid(string $guess)
    {
        $params = [
            'secret' => $this->appConfigCaptchaKey,
            'response' => $guess,
        ];

        $fullUrl = self::URI . '?' . \http_build_query($params);

        $verifyResponse = $this->httpClient->request('GET', $fullUrl);

        $responseData = \json_decode($verifyResponse->getBody());

        return !!$responseData->success;
    }
}
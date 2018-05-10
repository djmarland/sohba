<?php
declare(strict_types=1);

namespace App\Data;

class Captcha
{
    private const URI = 'https://www.google.com/recaptcha/api/siteverify';

    private $appConfigCaptchaKey;

    public function __construct(
        string $appConfigCaptchaKey
    ) {
        $this->appConfigCaptchaKey = $appConfigCaptchaKey;
    }

    public function isValid(string $guess)
    {
        $params = [
            'secret' => $this->appConfigCaptchaKey,
            'response' => $guess,
        ];

        $fullUrl = self::URI . '?' . \http_build_query($params);

        $verifyResponse = \file_get_contents($fullUrl); // todo future - guzzle
        $responseData = \json_decode($verifyResponse);

        return !!$responseData->success;
    }
}
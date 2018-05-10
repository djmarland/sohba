<?php
declare(strict_types=1);

namespace App\Service;

use App\Domain\Exception\CaptchaException;
use Swift_Message;
use Symfony\Component\HttpFoundation\Request;

class RequestsService extends AbstractService
{
    public function handleSubmission(Request $request): void
    {
        $this->handleCaptcha($request);

        // parse the incoming form
        $patientName = $request->get('name', '');
        $hospital = $request->get('hospital', '');
        $song = $request->get('request', '');
        $message = $request->get('message', '');

        $message = new Swift_Message(
            'Request',
            'A request with ' . $patientName . $hospital . $song . $message
        );
        $message->addFrom(
            'studio@sohba.org',
            'SOHBA Request'
        );
        $message->addTo('davidm@rland.me.uk');

        // send the e-mail
        $this->mailer->send($message);
    }

    private function handleCaptcha(Request $request)
    {
        $captcha = $request->get('g-recaptcha-response');
        if (!$captcha) {
            throw new CaptchaException('The Captcha was not supplied. Please try again');
        }

        if (!$this->captcha->isValid($captcha)) {
            throw new CaptchaException('Sorry, the Captcha was incorrect. Please try again');
        }
    }
}

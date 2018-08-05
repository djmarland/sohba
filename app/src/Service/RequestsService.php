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

        $now = new \DateTimeImmutable();

        $message = new Swift_Message(
            'Request from web: ' . $now->format('l jS F'),
            <<<BODY
A request was made on {$now->format('l jS F')} at {$now->format('H:s')}.

Patient Name: $patientName
Hospital: $hospital
Song: $song
Message: $message
BODY
        );
        $message->addFrom(
            $this->appConfigRequestFromAddress,
            'SOHBA Request'
        );
        $message->addTo($this->appConfigRequestToAddress);

        // send the e-mail
        $this->mailer->send($message);
    }

    private function handleCaptcha(Request $request)
    {
        if ($this->appConfigSkipCaptcha) {
            return;
        }

        $captcha = $request->get('g-recaptcha-response');
        if (!$captcha) {
            throw new CaptchaException('The Captcha was not supplied. Please try again');
        }

        if (!$this->captcha->isValid($captcha)) {
            throw new CaptchaException('Sorry, the Captcha was incorrect. Please try again');
        }
    }
}

<?php
declare(strict_types=1);

namespace App\Service;

use App\Domain\Exception\CaptchaException;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

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

        $now = new DateTimeImmutable();

        $body = <<<BODY
            A request was made on {$now->format('l jS F')} at {$now->format('H:s')}.
            
            Patient Name: $patientName
            Hospital: $hospital
            Song: $song
            Message: $message
            BODY;

        $message = (new Email())
            ->from(new Address(
                $this->appConfigRequestFromAddress,
                'SOHBA Request'
            ))
            ->to($this->appConfigRequestToAddress)
            ->subject('Request from web: ' . $now->format('l jS F'))
            ->text($body);

        // send the e-mail
        $this->mailer->send($message);
    }

    private function handleCaptcha(Request $request): void
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

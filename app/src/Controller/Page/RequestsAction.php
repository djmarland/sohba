<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Controller\AbstractController;
use App\Domain\Exception\CaptchaException;
use App\Service\RequestsService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestsAction extends AbstractController
{
    public function __invoke(
        Request $request,
        RequestsService $requestsService
    ): Response {

        $sent = false;
        $fail = false;
        if ($request->getMethod() === 'POST') {
            try {
                $requestsService->handleSubmission($request);
                $sent = true;
            } catch (CaptchaException $e) {
                $fail = $e->getMessage();
            } catch (\Throwable $e) {
                $fail = 'Sorry, there was an error sending your request. Please call 023 8078 5151. ' .
                    '(' . $e->getMessage() . ')';
            }
        }

        return $this->renderMainSite(
            'page/requests.html.twig',
            [
                'sent' => $sent,
                'fail' => $fail,
            ]
        );
    }
}

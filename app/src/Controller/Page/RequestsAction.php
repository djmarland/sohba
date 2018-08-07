<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Controller\AbstractController;
use App\Domain\Exception\CaptchaException;
use App\Presenter\Message\ErrorMessage;
use App\Presenter\Message\OkMessage;
use App\Service\PageService;
use App\Service\RequestsService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestsAction extends AbstractController
{
    public const SPECIAL_PAGE_URL = 'requests';

    public function __invoke(
        Request $request,
        RequestsService $requestsService,
        PageService $pageService
    ): Response {

        $message = null;
        if ($request->getMethod() === 'POST') {
            try {
                $requestsService->handleSubmission($request);
                $message = new OkMessage(
                    'Thank you. Your request was sent successfully. Keep listening.'
                );
            } catch (CaptchaException $e) {
                $message = new ErrorMessage($e->getMessage());
            } catch (\Throwable $e) {
                $message = new ErrorMessage(
                    'Sorry, there was an error sending your request. ' .
                    'Please try again or phone us to make your request. ' .
                    '(' . $e->getMessage() . ')'
                );
            }
        }

        return $this->renderMainSite(
            'page/requests.html.twig',
            [
                'message' => $message,
                'prose' => $pageService->findByUrl(self::SPECIAL_PAGE_URL),
            ]
        );
    }
}

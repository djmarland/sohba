<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Presenter\Message\ErrorMessage;
use App\Presenter\Message\OkMessage;
use App\Service\ConfigurableContentService;
use Exception;
use Ramsey\Uuid\UuidFactory;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function json_encode;

class KeyValueKeyAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        ConfigurableContentService $configurableContentService,
        UuidFactory $uuidFactory,
        string $id
    ): Response {
        $uuid = $uuidFactory->fromString($id);

        $content = $configurableContentService->findByUuid($uuid);
        if (!$content) {
            return $this->render404('No such page');
        }

        $message = null;
        if ($request->getMethod() === 'POST') {
            try {
                $configurableContentService->updateEntry(
                    $content,
                    $request->get('description'),
                    $request->get('html-content') ?? $request->get('value')
                );
                $message = new OkMessage('Saved');
            } catch (Exception $e) {
                $message = new ErrorMessage($e->getMessage());
            }

            // re-fetch the latest
            $content = $configurableContentService->findByUuid($uuid);
            if (!$content) {
                throw new RuntimeException('Something went very wrong here');
            }
        }


        return $this->renderAdminSite(
            'key-value-value.html.twig',
            [
                'pageData' => json_encode([
                    'message' => $message,
                    'content' => $content,
                ], JSON_PRETTY_PRINT),
                'content' => $content,
            ],
            $request
        );
    }
}

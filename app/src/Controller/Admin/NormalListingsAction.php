<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Service\SchedulesService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NormalListingsAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        SchedulesService $schedulesService,
        DateTimeImmutable $now
    ): Response {
        // redirect to the current day
        $dayName = strtolower($now->format('l'));
        return new RedirectResponse('/admin/normal-listings/' . $dayName, 302);
    }
}

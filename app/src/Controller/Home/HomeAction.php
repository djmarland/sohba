<?php
declare(strict_types=1);

namespace App\Controller\Home;

use App\Controller\AbstractController;
use App\Service\ConfigurableContentService as CCS;
use App\Service\PageService;
use App\Service\SchedulesService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeAction extends AbstractController
{
    public const SPECIAL_PAGE_URL = 'home';

    public function __invoke(
        Request $request,
        SchedulesService $schedulesService,
        PageService $pageService,
        CCS $configurableContentService,
        DateTimeImmutable $now
    ): Response {

        $phoneNumber = $configurableContentService->getValue(CCS::KEY_PHONE_NUMBER);

        return $this->renderMainSite(
            'home/home.html.twig',
            [
                'sports' => $schedulesService->findUpcomingSports($now, 3),
                'events' => $schedulesService->findUpcomingOutsideBroadcasts($now, 3),
                'prose' => $pageService->findByUrl(self::SPECIAL_PAGE_URL),
                'requestLineIntro' => $configurableContentService->getValue(CCS::KEY_REQUESTLINE_INTRO),
                'twitterUrl' => $configurableContentService->getValue(CCS::KEY_TWITTER_URL),
                'twitterText' => $configurableContentService->getValue(CCS::KEY_TWITTER_INTRO_TEXT),
                'facebookUrl' => $configurableContentService->getValue(CCS::KEY_FACEBOOK_URL),
                'facebookText' => $configurableContentService->getValue(CCS::KEY_FACEBOOK_INTRO_TEXT),
                'phoneNumberFormatted' => $phoneNumber,
                'phoneNumberRaw' => preg_replace('/[^0-9,.]/', '', $phoneNumber),
            ],
            $request
        );
    }
}

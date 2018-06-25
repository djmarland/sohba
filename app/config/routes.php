<?php
declare(strict_types=1);

use App\Controller;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

// home
$collection->add('home', new Route('/', [
    '_controller' => Controller\Home\HomeAction::class,
]));
$collection->add('styleguide', new Route('/styleguide', [
    '_controller' => Controller\Home\StyleguideAction::class,
]));

$schedulesPrefix = '/' . Controller\Schedules\AbstractSchedulesAction::SPECIAL_PAGE_URL;

$collection->add('schedule_today', new Route($schedulesPrefix, [
    '_controller' => Controller\Schedules\TodayAction::class,
]));

$collection->add('schedule_date', new Route($schedulesPrefix . '/{year}-{month}-{day}', [
    '_controller' => Controller\Schedules\DateAction::class,
], [
        'year' => '20[0-9][0-9]',
        'month' => '[01][0-9]',
        'day' => '[0123][0-9]',
    ]
));

$collection->add('schedule_day', new Route($schedulesPrefix . '/{day}', [
    '_controller' => Controller\Schedules\DayAction::class,
], [
    'day' => '(monday|tuesday|wednesday|thursday|friday|saturday|sunday)',
]));

$collection->add('images_show', new Route('/images/{width}/{id}.jpg', [
    '_controller' => Controller\Images\ShowAction::class,
], [
        'width' => '\d+',
        'id' => '[a-f0-9]{32}',
    ]
));

$collection->add('programmes_list', new Route('/programmes', [
    '_controller' => Controller\Programmes\ListAction::class,
]));

$collection->add('programmes_show', new Route('/programmes/{showId}', [
    '_controller' => Controller\Programmes\ShowAction::class,
    // todo - uuid
]));

$collection->add('page_people', new Route('/' . Controller\Page\PeopleAction::SPECIAL_PAGE_URL, [
    '_controller' => Controller\Page\PeopleAction::class,
]));

$collection->add('page_requests', new Route('/' . Controller\Page\RequestsAction::SPECIAL_PAGE_URL, [
    '_controller' => Controller\Page\RequestsAction::class,
]));

$collection->add('page_sports', new Route('/' . Controller\Page\SportsAction::SPECIAL_PAGE_URL, [
    '_controller' => Controller\Page\SportsAction::class,
]));

$collection->add('page_outside_broadcasts', new Route(
    '/' . Controller\Page\OutsideBroadcastsAction::SPECIAL_PAGE_URL,
    [
        '_controller' => Controller\Page\OutsideBroadcastsAction::class,
    ]
));


// Admin
$collection->add('admin_home', new Route('/admin2', [
    '_controller' => Controller\Admin\HomeAction::class,
]));
$collection->add('admin_pages', new Route('/admin/pages', [
    '_controller' => Controller\Admin\PagesAction::class,
]));
$collection->add('admin_page', new Route('/admin/pages/{pageId}', [
    '_controller' => Controller\Admin\PageAction::class,
]));
$collection->add('admin_images', new Route('/admin/images', [
    '_controller' => Controller\Admin\ImagesAction::class,
]));


$collection->add('page', new Route('/{page}', [
    '_controller' => Controller\Page\ShowAction::class,
]));

return $collection;

<?php
declare(strict_types=1);

namespace Tests\App\Presenter;

use App\Domain\Entity\Page;
use App\Domain\Entity\PageCategory;
use App\Presenter\NavigationPresenter;
use Ramsey\Uuid\Uuid;

class NavigationPresenterTest extends \PHPUnit\Framework\TestCase
{
    public function testGroupsBrokenOut()
    {
        $group1 = new PageCategory(
            $this->createMock(Uuid::class),
            $groupTitle1 = 'group1'
        );
        $group2 = new PageCategory(
            $this->createMock(Uuid::class),
            $groupTitle2 = 'group2'
        );

        $input = [
            new Page(
                $this->createMock(Uuid::class),
                1,
                $pageTitle1 = 'Page1',
                $group1
            ),
            new Page(
                $this->createMock(Uuid::class),
                2,
                $pageTitle2 = 'Page2',
                $group1
            ),
            new Page(
                $this->createMock(Uuid::class),
                3,
                $pageTitle3 = 'Page3',
                $group2
            )
        ];

        $presenter = new NavigationPresenter($input);

        $this->assertCount(2, $presenter->getGroups());

        $this->assertSame($groupTitle1, $presenter->getGroups()[0]['title']);
        $this->assertSame($groupTitle2, $presenter->getGroups()[1]['title']);

        $this->assertCount(2, $presenter->getGroups()[0]['pages']);
        $this->assertCount(1, $presenter->getGroups()[1]['pages']);

        $this->assertSame($pageTitle1, $presenter->getGroups()[0]['pages'][0]->getTitle());
        $this->assertSame($pageTitle2, $presenter->getGroups()[0]['pages'][1]->getTitle());
        $this->assertSame($pageTitle3, $presenter->getGroups()[1]['pages'][0]->getTitle());
    }
}
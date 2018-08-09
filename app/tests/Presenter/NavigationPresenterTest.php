<?php
declare(strict_types=1);

namespace Tests\App\Presenter;

use App\Domain\Entity\Page;
use App\Domain\Entity\PageCategory;
use App\Domain\ValueObject\RichText;
use App\Presenter\NavigationPresenter;
use Ramsey\Uuid\Uuid;

class NavigationPresenterTest extends \Tests\App\BaseTestCase
{
    public function testGroupsBrokenOut()
    {
        $group1 = new PageCategory(
            $this->createMock(Uuid::class),
            $this->faker->randomNumber(8),
            $groupTitle1 = $this->faker->text(50)
        );
        $group2 = new PageCategory(
            $this->createMock(Uuid::class),
            $this->faker->randomNumber(8),
            $groupTitle2 = $this->faker->text(50)
        );

        $input = [
            new Page(
                $this->createMock(Uuid::class),
                $this->faker->randomNumber(8),
                $pageTitle1 = $this->faker->text(50),
                $this->createMock(RichText::class),
                $this->faker->text(20),
                $this->faker->randomNumber(3),
                $group1
            ),
            new Page(
                $this->createMock(Uuid::class),
                $this->faker->randomNumber(8),
                $pageTitle2 = $this->faker->text(50),
                $this->createMock(RichText::class),
                $this->faker->text(20),
                $this->faker->randomNumber(3),
                $group1
            ),
            new Page(
                $this->createMock(Uuid::class),
                $this->faker->randomNumber(8),
                $pageTitle3 = $this->faker->text(50),
                $this->createMock(RichText::class),
                $this->faker->text(20),
                $this->faker->randomNumber(3),
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

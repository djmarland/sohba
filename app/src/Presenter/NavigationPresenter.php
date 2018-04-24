<?php
declare(strict_types=1);

namespace App\Presenter;

use App\Domain\Entity\Page;

class NavigationPresenter
{
    private $groups;

    public function __construct(array $pagesWithCategories)
    {
        $this->groups = $this->calculate($pagesWithCategories);
    }

    public function getGroups(): array
    {
        return $this->groups;
    }

    private function calculate(array $pagesWithCategories)
    {
        // rearrange into a nested set of groups. they should already be in the right order
        $groups = [];
        $i = -1;
        foreach ($pagesWithCategories as $page) {
            /*** @var $page Page */
            if (!isset($groups[$i])
                || !$page->getCategory()->equals($groups[$i]['group'])
            ) {
                $i++;
                $groups[$i] = [
                    'title' => $page->getCategory()->getTitle(),
                    'group' => $page->getCategory(),
                    'pages' => [],
                ];
            }
            $groups[$i]['pages'][] = $page;
        }
        return $groups;
    }
}

<?php
declare(strict_types=1);

namespace Tests\App;

use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    protected Generator $faker;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->faker = Factory::create('en_GB');
    }
}

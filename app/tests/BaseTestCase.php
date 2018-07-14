<?php
declare(strict_types=1);

namespace Tests\App;

abstract class BaseTestCase extends \PHPUnit\Framework\TestCase
{
    protected $faker;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->faker = \Faker\Factory::create('en_GB');
    }
}

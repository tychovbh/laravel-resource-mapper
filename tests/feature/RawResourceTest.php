<?php
declare(strict_types=1);

namespace Tychovbh\Tests\ResourceMapper\Feature;

use Faker\Provider\Company;
use Faker\Provider\Person;
use Tychovbh\ResourceMapper\RawResource;
use Tychovbh\Tests\ResourceMapper\TestCase;

class RawResourceTest extends TestCase
{
    /**
     * Get dummy resource
     * @return array
     */
    private function resource(): array
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new Person($faker));
        $faker->addProvider(new Company($faker));
        return [
            'title' => $faker->title,
            'firstname' => $faker->firstName,
            'suffix' => $faker->suffix,
            'lastname' => $faker->lastname,
            'company' => [
                'name' => $faker->company
            ]
        ];
    }

    /**
     * @test
     */
    public function itCanInitialize()
    {
        $mapped = new RawResource($this->resource());
        $this->assertInstanceOf(RawResource::class, $mapped);
    }

    /**
     * @test
     */
    public function itCanGetValueFromResource()
    {
        $rawResource = $this->resource();
        $mapped = new RawResource($rawResource);
        $this->assertEquals($rawResource['title'], $mapped->get('title'));
    }

    /**
     * @test
     */
    public function itCanGetRecursiveValueFromResource()
    {
        $rawResource = $this->resource();
        $mapped = new RawResource($rawResource);
        $this->assertEquals($rawResource['company']['name'], $mapped->get('company.name'));
    }

    /**
     * @test
     */
    public function itCanJoinValuesFromResource()
    {
        $rawResource = $this->resource();
        $mapped = new RawResource($rawResource);
        $fullname = sprintf('%s %s %s', $rawResource['firstname'], $rawResource['suffix'], $rawResource['lastname']);
        $this->assertEquals($fullname, $mapped->join(' ', 'firstname', 'suffix', 'lastname'));
    }
}

<?php
declare(strict_types=1);

namespace Tychovbh\Tests\ResourceMapper\Feature;

use Faker\Provider\Company;
use Faker\Provider\Person;
use Tychovbh\ResourceMapper\RawResource;
use Tychovbh\ResourceMapper\ResourceMapper;
use Tychovbh\Tests\ResourceMapper\TestCase;

class ResourceMapperTest extends TestCase
{
    /**
     * @test
     */
    public function itCanInitialize()
    {
        $mapper = app('resource-mapper');
        $this->assertInstanceOf(ResourceMapper::class, $mapper);
        return $mapper;
    }

    /**
     * Get dummy resource
     * @return array
     */
    private function resource()
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new Person($faker));
        $faker->addProvider(new Company($faker));
        return [
            'id' => $faker->Uuid,
            'title' => $faker->title,
            'firstname' => $faker->firstName,
            'suffix' => $faker->suffix,
            'lastname' => $faker->lastname,
            'company' => $faker->company
        ];
    }

    /**
     * Get raw response
     * @param array $resource
     * @return array
     */
    private function raw(array $resource): array
    {
        return [
            'RawID' => $resource['id'],
            'RawTitle' => strtolower($resource['title']),
            'RawFirstname' => $resource['firstname'],
            'RawSuffix' => $resource['suffix'],
            'RawLastname' => $resource['lastname'],
            'RawCompany' => [
                'RawName' => $resource['company']
            ]
        ];
    }

    /**
     * Assert if response resource is actually mapped
     * @param array $resource
     * @param array $response
     */
    private function assertResponse(array $resource, array $response)
    {
        $this->assertEquals([
            'id' => $resource['id'],
            'company' => $resource['company'],
            'title' => $resource['title'],
            'fullname' => sprintf('%s %s %s', $resource['firstname'], $resource['suffix'], $resource['lastname'])
        ], $response);
    }

    /**
     * @test
     * @depends itCanInitialize
     * @param ResourceMapper $mapper
     */
    public function itCanMapRawResourceViaConfig(ResourceMapper $mapper)
    {
        $resource = $this->resource();
        $response = $mapper
            ->config('user')
            ->map($this->raw($resource));

        $this->assertResponse($resource, $response);
    }

    /**
     * @test
     * @depends itCanInitialize
     * @param ResourceMapper $mapper
     */
    public function itCanMapResourceViaMapping(ResourceMapper $mapper)
    {
        $resource = $this->resource();

        $response = $mapper->mapping([
            'id' => 'RawID',
            'company' => 'RawCompany.RawName',
            'title' => function (RawResource $resource) {
                return ucfirst($resource->get('RawTitle'));
            },
            'fullname' => function (RawResource $resource) {
                return $resource->join(' ', 'RawFirstname', 'RawSuffix', 'RawLastname');
            }
        ])->map($this->raw($resource));

        $this->assertResponse($resource, $response);
    }

    /**
     * @test
     * @depends itCanInitialize
     * @param ResourceMapper $mapper
     */
    public function itCanMapJson(ResourceMapper $mapper)
    {
        $resource = $this->resource();
        $response = $mapper
            ->config('user')
            ->mapJson(json_encode($this->raw($resource)));

        $this->assertResponse($resource, $response);
    }

    /**
     * @test
     * @depends itCanInitialize
     * @param ResourceMapper $mapper
     */
    public function itCanMapCollection(ResourceMapper $mapper)
    {
        $collection = [$this->resource(), $this->resource()];
        $response = $mapper
            ->config('user')
            ->mapCollection([
                $this->raw($collection[0]),
                $this->raw($collection[1])
            ]);

        foreach ($collection as $key => $item) {
            $this->assertResponse($item, $response[$key]);
        }
    }

    /**
     * @test
     * @depends itCanInitialize
     * @param ResourceMapper $mapper
     */
    public function itCanMapRecursive(ResourceMapper $mapper)
    {
        $resource = $this->resource();

        $response = $mapper->config('company_user')->map([
            'RawCompany' => [
                'RawUser' => $this->raw($resource),
            ]
        ]);

        $this->assertArrayHasKey('user', $response);
        $this->assertResponse($resource, $response['user']);
    }

    /**
     * @test
     * @depends itCanInitialize
     * @param ResourceMapper $mapper
     */
    public function itCanMapRecursiveCollection(ResourceMapper $mapper)
    {
        $collection = [$this->resource(), $this->resource()];

        $response = $mapper->config('users')->map([
            'RawItems' => [
                $this->raw($collection[0]),
                $this->raw($collection[1])
            ]
        ]);

        $this->assertArrayHasKey('items', $response);
        foreach ($response['items'] as $key => $user) {
            $this->assertResponse($collection[$key], $user);
        }
    }
}

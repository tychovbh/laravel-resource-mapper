<?php

declare(strict_types=1);

namespace Tychovbh\Tests\ResourceMapper;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Tychovbh\ResourceMapper\RawResource;

class TestCase extends BaseTestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('resource-mapper', [
            'user' => [
                'id' => 'RawID',
                'company' => 'RawCompany.RawName',
                'title' => function (RawResource $resource) {
                    return ucfirst($resource->get('RawTitle'));
                },
                'fullname' => function (RawResource $resource) {
                    return $resource->join(' ', 'RawFirstname', 'RawSuffix', 'RawLastname');
                },
            ],
            'company_user' => [
                'user' => function (RawResource $resource) {
                    return $this->config('user')->map($resource->get('RawCompany.RawUser'));
                }
            ],
            'users' => [
                'items' => function (RawResource $resource) {
                    return $this->config('user')->mapCollection($resource->get('RawItems'));
                }
            ]
        ]);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [\Tychovbh\ResourceMapper\ResourceMapperServiceProvider::class];
    }
}

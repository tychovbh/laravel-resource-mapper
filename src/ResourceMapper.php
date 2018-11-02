<?php
declare(strict_types=1);

namespace Tychovbh\ResourceMapper;

use Closure;
use Illuminate\Contracts\Config\Repository;

class ResourceMapper
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $mapping = [];

    /**
     * ResourceMapper constructor.
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config->get('resource-mapper');
    }

    /**
     * @param string $mapper
     * @return $this
     */
    public function config(string $mapper): ResourceMapper
    {
        $this->mapping = array_get($this->config, $mapper);
        return $this;
    }

    /**
     * Skip config and use mapping directly
     * @param array $mapping
     * @return $this
     */
    public function mapping(array $mapping): ResourceMapper
    {
        $this->mapping = $mapping;
        return $this;
    }

    /**
     * Map resource from json
     * @param string $resource
     * @return array
     */
    public function mapJson(string $resource): array
    {
        return $this->map(json_decode($resource, true));
    }

    /**
     * Map a collection of items
     * @param array $rawCollection
     * @return array
     */
    public function mapCollection(array $rawCollection)
    {
        $collection = [];
        foreach ($rawCollection as $item) {
            $collection[] = $this->map($item);
        }

        return $collection;
    }

    /**
     * Map raw resource
     * @param array $resource
     * @return array
     */
    public function map(array $resource): array
    {
        $rawResource = new RawResource($resource);
        $mapped = [];
        foreach ($this->mapping as $key => $location) {
            if (is_callable($location)) {
                $mapper = app('resource-mapper');
                $mapped[$key] = Closure::fromCallable($location)->call($mapper, $rawResource);
                continue;
            }
            $mapped[$key] = $rawResource->get($location);
        }

        return $mapped;
    }
}

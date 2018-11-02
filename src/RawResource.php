<?php
declare(strict_types=1);

namespace Tychovbh\ResourceMapper;

class RawResource
{
    /**
     * @var array
     */
    private $resource = [];

    /**
     * RawResource constructor.
     * @param array $resource
     */
    public function __construct(array $resource = [])
    {
        $this->resource = $resource;
    }

    /**
     * @param array $resource
     */
    public function resource(array $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Get value from resource
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return array_get($this->resource, $key);
    }

    /**
     * Join multiple keys together
     * @param string $delimiter
     * @param mixed ...$keys
     * @return string
     */
    public function join(string $delimiter, ...$keys): string
    {
        return implode($delimiter, array_map(function ($key) {
            return $this->get($key);
        }, $keys));
    }
}

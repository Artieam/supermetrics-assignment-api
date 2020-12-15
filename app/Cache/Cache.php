<?php

declare(strict_types=1);

namespace app\Cache;

use app\Client\ApiClient;
use app\Models\Posts;

/**
 * Write here new cacheable methods and class
 *
 * @method string obtainToken()
 * @method Posts getPosts()
 * @see ApiClient
 */
class Cache
{
    private object $realObject;
    private CacheStorage $cacheStorage;
    private int $ttl;
    private bool $force;

    public function __construct(object $object, int $ttl = 60, bool $force = false)
    {
        $this->realObject = $object;
        $this->cacheStorage = new CacheStorage();
        $this->ttl = $ttl;
        $this->force = $force;
    }

    /**
     * Intercept calls to non-existent proxy methods and broadcast them to the real object by magic method __call()
     *
     * @param string $method
     * @param array $args
     *
     * @return mixed
     * @throws \JsonException
     */
    public function __call(string $method, array $args)
    {
        $cacheKey = $this->configureCacheKeyByMethod($method, $args);
        $data = $this->getCache($cacheKey);

        if (! $data || $this->force) {
            $call = [$this->realObject, $method];
            $data = \call_user_func_array($call, $args);
            $this->cacheStorage->put($cacheKey, \serialize($data), $this->ttl);
        }

        return $data;
    }

    /**
     * Set Time to live
     *
     * @param int $ttl
     */
    public function setTtl(int $ttl): void
    {
        $this->ttl = $ttl;
    }

    /**
     * Set parameter for calling realObject method forcibly
     *
     * @param bool $force
     */
    public function setForce(bool $force): void
    {
        $this->force = $force;
    }

    /**
     * Get Real Object
     *
     * @return object
     */
    public function getRealObject(): object
    {
        return $this->realObject;
    }

    /**
     * @param string $method
     * @param array $args
     *
     * @return string
     */
    public function configureCacheKeyByMethod(string $method, array $args): string
    {
        return 'cached_' . $method . (empty($args) ? '' : '(' . \serialize($args) . ')');
    }

    /**
     * @param string $cacheKey
     *
     * @return mixed
     */
    public function getCache(string $cacheKey)
    {
        return \unserialize($this->cacheStorage->get($cacheKey) ?? '', [true]);
    }
}

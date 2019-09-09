<?php


namespace Crawling_WP;


class ProxyCacheService implements ProxyInterface
{

    const CACHE_PREFIX   = 'crawling_ip';
    const CACHE_TIME     = 15;
    const RE_USAGE_COUNT = 5;

    /**
     * @var object
     */
    private $proxy;

    /**
     * ProxyCacheService constructor.
     */
    public function __construct()
    {
        $this->proxy = $this->getProxy();
    }

    /**
     * @return object|null
     */
    public function getProxyFromCache()
    {
        $proxies = get_transient(ProxyService::PROXY_CACHE);

        if (! is_array($proxies)) {
            return null;
        }

        return $proxies[array_rand($proxies)];
    }

    /**
     * @return object|null
     */
    protected function getProxy()
    {
        $used = get_transient(PREFIX.self::CACHE_PREFIX);

        if ($used) {
            if ($used->count <= self::RE_USAGE_COUNT) {
                $used->count++;
                set_transient(PREFIX.self::CACHE_PREFIX, $used, self::CACHE_TIME);

                return $used->proxy;
            }
        }

        $proxy = $this->getProxyFromCache();
        if (! $proxy) {
            return null;
        }

        set_transient(PREFIX.self::CACHE_PREFIX, (object)[
            'count' => 1,
            'proxy' => $proxy
        ], self::CACHE_TIME);

        return $proxy;
    }


    /**
     * Proxy string for curl
     *
     * @return bool|string
     */
    public function getProxyString()
    {
        if (is_null($this->proxy)) {
            return false;
        }

        return $this->proxy['proxy'];
    }

    /**
     * @return bool|int
     */
    public function getCurlProxyType()
    {
        if (is_null($this->proxy)) {
            return false;
        }

        return $this->proxy['type'];
    }
}
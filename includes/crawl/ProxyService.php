<?php


namespace Crawling_WP;


use Exception;

class ProxyService implements ProxyInterface
{

    const PROXY_CACHE = 'crawling_proxy';
    const CACHE_TIME  = 10;

    /**
     * @var string
     */
    private $key;

    /**
     * @var object
     */
    private $proxy;

    /**
     * ProxyService constructor.
     * @param $key
     * @throws Exception
     */
    public function __construct($key)
    {
        $this->key   = $key;
        $this->proxy = $this->getProxy();
    }

    /**
     * @return object|null
     */
    public function getProxyFromApi()
    {
        $check = 0;
        $proxy = $this->requestProxy();

        while (! self::checkProxy($proxy->ip, $proxy->port)) {
            $proxy = $this->requestProxy();

            if ($check > 10) {
                return null;
            }
            $check++;
        }

        return $proxy;
    }

    /**
     * @return object
     */
    protected function requestProxy()
    {
        $response = @file_get_contents('https://api.getproxylist.com/proxy?'.$this->getParamsString());

        return $response ? json_decode($response) : (object)['ip' => false, 'port' => false];
    }

    /**
     * @return object|null
     */
    protected function getProxy()
    {
//        $used = get_transient(PREFIX.self::CACHE_PREFIX);
//
//        if ($used) {
//            if ($used->count <= self::RE_USAGE_COUNT) {
//                $used->count++;
//                set_transient(PREFIX.self::CACHE_PREFIX, $used, self::CACHE_TIME);
//
//                return $used->proxy;
//            }
//        }

        $proxy = $this->getProxyFromApi();
        if (! $proxy) {
            return null;
        }

//        set_transient(PREFIX.self::CACHE_PREFIX, (object)[
//            'count' => 1,
//            'proxy' => $proxy
//        ], self::CACHE_TIME);

        return $proxy;
    }

    /**
     * @param $host
     * @param $port
     * @return bool
     */
    public static function checkProxy($host, $port)
    {
        if (! $host || ! $port) {
            return false;
        }

        if ($fp = @fsockopen($host, $port, $errCode, $errStr, 5)) {
            fclose($fp);

            return true;
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    protected function getParamsString()
    {
        $params = [
            'lastTested'            => 600,
            'allowsPost'            => 1,
            'allowsHttps'           => 1,
            'protocol'              => 'http',
            'maxSecondsToFirstByte' => 5
        ];

        if ($this->key) {
            $params['apiKey'] = $this->key;
        }

        return http_build_query($params);
    }

    /**
     * Proxy string for curl
     *
     * @return bool|string
     */
    public function getProxyString()
    {
        if (! is_object($this->proxy)) {
            return false;
        }

        return $this->proxy->ip.':'.$this->proxy->port;
    }

    /**
     * @return bool|int
     */
    public function getCurlProxyType()
    {
        if (! is_object($this->proxy)) {
            return false;
        }

        return constant('CURLPROXY_'.strtoupper($this->proxy->protocol));
    }
}
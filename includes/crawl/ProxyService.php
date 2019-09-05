<?php


namespace Crawling_WP;


use Exception;

class ProxyService
{

    const CACHE_PREFIX = 'crawling_ip_';
    const CACHE_TIME   = 15;

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
        $this->key = $key;

        $proxy = $this->getProxy();

        $check = 1;

        while (! self::checkProxy($proxy->ip, $proxy->port)) {
            $proxy = $this->getProxy();

            if ($check > 10) {
                throw new Exception('Could not get HTTP Proxy');
            }
            $check++;
        }

        $this->proxy = $proxy;
    }

    /**
     * @return object
     */
    protected function getProxy()
    {
        $response = @file_get_contents('https://api.getproxylist.com/proxy?'.$this->getParamsString());

        return $response ? json_decode($response) : (object)['ip' => false, 'port' => false];
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

        if (self::isUsedProxy($host)) {
            return false;
        }

        $waitTimeoutInSeconds = 5;
        if ($fp = @fsockopen($host, $port, $errCode, $errStr, $waitTimeoutInSeconds)) {
            fclose($fp);
            self::addProxyToUsedList($host);

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
            'allowsHttps' => 1,
            'protocol'    => 'http'
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

    /**
     * Check is host at cache list
     *
     * @param string $host
     * @return bool
     */
    public static function isUsedProxy($host)
    {
        return get_transient(self::CACHE_PREFIX.$host) !== false;
    }

    /**
     * Add Proxy To Cache
     *
     * @param string $host
     */
    public static function addProxyToUsedList($host)
    {
        set_transient(self::CACHE_PREFIX.$host, true, self::CACHE_TIME);
    }
}
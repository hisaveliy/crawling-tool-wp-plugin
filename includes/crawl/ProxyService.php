<?php


namespace Crawling_WP;


class ProxyService
{

    private $key;

    /**
     * @var false|string|void
     */
    private $proxy;

    public function __construct($key)
    {
        $this->key   = $key;
        $this->proxy = $this->getProxy();
    }

    /**
     * @return false|mixed|string|void
     */
    protected function getProxy()
    {
        $response = file_get_contents('https://api.getproxylist.com/proxy?allowsHttps=1&protocol=http'.$this->getApiKeyParam());

        return json_decode($response);
    }

    /**
     * @return string
     */
    protected function getApiKeyParam()
    {
        if ($this->key) {
            return '&apiKey='.$this->key;
        }

        return '';
    }

    /**
     * @return bool|string
     */
    public function getProxyString()
    {
        if (! is_object($this->proxy)) {
            return false;
        }

        return $this->proxy->ip.':'.$this->proxy->port;
    }
}
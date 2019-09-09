<?php


namespace Crawling_WP;


interface ProxyInterface
{

    public function getProxyString();

    public function getCurlProxyType();
}
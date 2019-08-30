<?php


namespace Crawling_WP;


abstract class BaseWebsite
{

    abstract public function getHtml();

    abstract public function getEstates();

    abstract protected function getUrl();
}
<?php

if (! defined('Crawling_WP\PLUGIN_DIR')) {
    define('Crawling_WP\PLUGIN_DIR', '../../');
}


use const Crawling_WP\PLUGIN_DIR;

$plugin_classes = [
    \Crawling_WP\ContactsEstate::class,
    \Crawling_WP\BaseWebsite::class,
    \Crawling_WP\DeutscheWohnen::class,
    \Crawling_WP\AddressEstate::class,
    \Crawling_WP\GalleryEstate::class,
    \Crawling_WP\DetailsEstate::class,
    \Crawling_WP\CrawlHelper::class,
    \Crawling_WP\RentEstate::class,
    \Crawling_WP\TermEstate::class,
    \Crawling_WP\Estate::class,
    \Crawling_WP\Wohnraumkarte::class,
    \Crawling_WP\WohnraumkartePaginator::class,
    \Crawling_WP\ProxyInterface::class,
    \Crawling_WP\ProxyService::class,
    \Crawling_WP\ProxyCacheService::class,
];


foreach ($plugin_classes as $class) {
    include_once PLUGIN_DIR.'/includes/crawl/'.substr(strrchr($class, "\\"), 1).'.php';
}
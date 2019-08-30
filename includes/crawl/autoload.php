<?php

use const Crawling_WP\PLUGIN_DIR;

$plugin_classes = [
    \Crawling_WP\ContactsEstate::class,
    \Crawling_WP\DeutscheWohnen::class,
    \Crawling_WP\AddressEstate::class,
    \Crawling_WP\GalleryEstate::class,
    \Crawling_WP\DetailsEstate::class,
    \Crawling_WP\CrawlHelper::class,
    \Crawling_WP\RentEstate::class,
    \Crawling_WP\TermEstate::class,
    \Crawling_WP\Estate::class,
];


foreach ($plugin_classes as $class) {
    include_once PLUGIN_DIR.'/includes/crawl/'.substr(strrchr($class, "\\"), 1).'.php';
}
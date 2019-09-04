<?php


namespace Crawling_WP;


abstract class BaseWebsite
{

    abstract public function getEstateHtml($crawl_id);

    abstract public function updateEstates();

    abstract public function addEstate($estate);

    abstract protected function getUrl();

    abstract public static function getEstateAddress($html): AddressEstate;

    abstract public static function getEstateGallery(array $images): GalleryEstate;

    abstract public static function getEstateRent($estate): RentEstate;

    abstract public static function getEstateDetails($estate): DetailsEstate;

    abstract public static function getEstateDescription($estate_id);

    abstract public static function getEstateTerms($estate): TermEstate;
}
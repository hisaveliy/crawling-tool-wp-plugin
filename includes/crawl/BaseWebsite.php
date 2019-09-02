<?php


namespace Crawling_WP;


abstract class BaseWebsite
{

    abstract public function getEstateHtml($crawl_id);

    abstract public function updateEstates();

    abstract protected function getUrl();

    abstract public static function isEstateExist($estate_id);

    abstract public static function getEstateAddress($html): AddressEstate;

    abstract public static function getEstateGallery(array $images): GalleryEstate;

    abstract public static function getEstateRent($estate): RentEstate;

    abstract public static function getEstateDetails($estate): DetailsEstate;

    abstract public static function getEstateDescription($estate_id);

    abstract public static function getEstateTerms($estate): TermEstate;

    abstract public static function getHeating($html);

    abstract protected static function getFeatures($html);
}
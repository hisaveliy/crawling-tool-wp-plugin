<?php


namespace Crawling_WP;

use Exception;

class DeutscheWohnen extends BaseWebsite
{

    const PREFIX = 'deutschewohnen';

    /**
     * @var ProxyService
     */
    private $proxyService;

    /**
     * DeutscheWohnen constructor.
     * @param ProxyInterface $proxyService
     */
    public function __construct(ProxyInterface $proxyService)
    {
        $this->proxyService = $proxyService;
    }

    /**
     * @return bool|string
     * @throws Exception
     */
    public function getHtml()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->getUrl());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getBody());
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
        if ($this->proxyService) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxyService->getProxyString());
            curl_setopt($ch, CURLOPT_PROXYTYPE, $this->proxyService->getCurlProxyType());
        }

        $result = curl_exec($ch);

        if (curl_error($ch) !== "") {
            throw new Exception(curl_error($ch));
        };

        curl_close($ch);

        return $result;
    }

    /**
     * Update All Estates
     */
    public function updateEstates()
    {
        try {
            $list = json_decode($this->getHtml());

            foreach ($list as $estate) {
                $id = CrawlHelper::isEstateExist($estate->id, self::PREFIX);

                if ($id) {
                    if (RentEstate::getTotalRent($id) !== $estate->price) {
                        self::getEstateRent($estate)->save($id);
                    }
                    continue;
                } else {
                    $this->addEstate($estate);
                }
            }

        } catch (Exception $e) {
            error_log($e->getMessage(), null, $e->getTraceAsString(), $e->getFile());
        }
    }

    /**
     * @param $estate
     * @return Estate|null|bool
     */
    public function addEstate($estate)
    {
        try {
            $html = $this->getEstateHtml($estate->id);
        } catch (Exception $e) {
            error_log($e->getMessage(), null, $e->getTraceAsString(), $e->getFile());

            return null;
        }

        $id = CrawlHelper::isEstateExist($estate->id, self::PREFIX);

        if ($id) {
            return true;
        }

        $address     = self::getEstateAddress($estate);
        $gallery     = self::getEstateGallery($estate->images);
        $rent        = self::getEstateRent($html);
        $description = self::getEstateDescription($html);

        $details = self::getEstateDetails($html);
        $details->setArea($estate->area)->setRooms($estate->rooms);

        $terms = self::getEstateTerms($html);
        $terms->add('iwp_status', strtolower($estate->commercializationType));

        $contacts = new ContactsEstate(true, false, false, true);

        $entity = new Estate(
            false,
            $estate->title,
            $description,
            $address,
            $gallery,
            $contacts,
            $details,
            $rent,
            $terms,
            $estate->id,
            self::PREFIX);

        $entity->save();

        return $entity;
    }

    /**
     * @param $estate
     * @return AddressEstate
     */
    public static function getEstateAddress($estate): AddressEstate
    {
        $street = $estate->address->street.' '.$estate->address->houseNumber;

        return new AddressEstate($street, $estate->address->zip, implode(',', (array)$estate->geoLocation));
    }

    /**
     * @param array $images
     * @return GalleryEstate
     */
    public static function getEstateGallery(array $images): GalleryEstate
    {
        $gallery = new GalleryEstate();

        foreach ($images as $img) {
            $gallery->addImage(
                'https://immo-api.deutsche-wohnen.com'.$img->filePath,
                $img->title,
                ''
            );
        }

        return $gallery;
    }

    /**
     * @param $html
     * @return RentEstate
     */
    public static function getEstateRent($html): RentEstate
    {
        $price     = self::toFloat(CrawlHelper::getTableValue($html, 'Kaltmiete'));
        $addtional = self::toFloat(CrawlHelper::getTableValue($html, 'Nebenkosten'));
        $deposit   = self::toFloat(CrawlHelper::getTableValue($html, 'Kaution'));

        return new RentEstate($price, $addtional, $deposit);
    }

    /**
     * @param $html
     * @return DetailsEstate
     */
    public static function getEstateDetails($html): DetailsEstate
    {
        $heating = self::toFloat(CrawlHelper::getTableValue($html, 'Verbrauchswert'));
        $date    = CrawlHelper::getTableValue($html, 'Verfügbar ab');

        return new DetailsEstate($date, null, null, $heating);
    }

    /**
     * @param string $str
     * @return float
     */
    public static function toFloat($str)
    {
        $str = str_replace('.', '', $str);
        $str = str_replace(',', '.', $str);

        return floatval($str);
    }

    /**
     * @param $html
     * @return bool|string
     */
    public static function getEstateDescription($html)
    {
        $startDivTag = 'object-detail__description';

        $startDiv = strpos($html, $startDivTag);
        if (! $startDiv) {
            return '';
        }
        $start = strpos($html, '<p>', $startDiv) + 3;
        if ($start === 3) {
            return '';
        }
        $end = strpos($html, '</p>', $start);

        return substr($html, $start, $end - $start);
    }

    /**
     * @param $html
     * @return TermEstate
     */
    public static function getEstateTerms($html): TermEstate
    {
        $terms = new TermEstate();

        $terms->add('iwp_features', self::getFeatures($html));
        $terms->add('iwp_heatingtype', self::getHeating($html));

        return $terms;
    }

    /**
     * @param $html
     * @return string
     */
    public static function getHeating($html)
    {
        return CrawlHelper::getTableValue($html, 'Heizungsart');
    }

    /**
     * @param $html
     * @return array
     */
    protected static function getFeatures($html)
    {
        $start_block = 'object-detail__equipment-icons">';

        $start = strpos($html, $start_block) + strlen($start_block);
        if ($start === strlen($start_block)) {
            return [];
        }

        $end = strpos($html, '</ul>', $start);

        $html = substr($html, $start, $end - $start);

        return CrawlHelper::getContentByAttribute($html, 'object-detail__equipment-description');
    }

    /**
     * @return string
     */
    protected function getBody()
    {
        return '{"infrastructure":{},"flatTypes":{},"other":{},"commercializationType":"rent","utilizationType":"flat","location":"Berlin"}';
    }

    /**
     * @return string
     */
    protected function getUrl()
    {
        return 'https://immo-api.deutsche-wohnen.com/estate/findByFilter';
    }

    /**
     * @param $crawl_id
     * @return bool|string
     * @throws Exception
     */
    public function getEstateHtml($crawl_id)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://www.deutsche-wohnen.com/expose/object/'.$crawl_id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($this->proxyService) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxyService->getProxyString());
            curl_setopt($ch, CURLOPT_PROXYTYPE, $this->proxyService->getCurlProxyType());
        }

        $result = curl_exec($ch);

        if (curl_error($ch) !== "") {
            throw new Exception(curl_error($ch));
        };

        curl_close($ch);

        return $result;
    }
}
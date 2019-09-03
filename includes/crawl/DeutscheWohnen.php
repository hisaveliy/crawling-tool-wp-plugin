<?php


namespace Crawling_WP;

use Exception;

class DeutscheWohnen
{

    const PREFIX = 'deutschewohnen';

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

        $result = curl_exec($ch);

        if (curl_error($ch) !== "") {
            throw new Exception(curl_error($ch));
        };

        curl_close($ch);

        return $result;
    }

    public function updateEstates()
    {
        try {
            $list = json_decode($this->getHtml());

            foreach ($list as $estete) {
                $id = CrawlHelper::isEstateExist($estete->id, self::PREFIX);

                if ($id) {
                    if (RentEstate::getTotalRent($id) !== $estete->price) {
                        self::getEstateRent($estete)->save($id);
                    }
                    continue;
                } else {
                    $address     = self::getEstateAddress($estete);
                    $gallery     = self::getEstateGallery($estete->images);
                    $rent        = self::getEstateRent($estete);
                    $details     = self::getEstateDetails($estete);
                    $description = self::getEstateDescription($estete->id);
                    $terms       = self::getEstateTerms($estete);
                    $contacts    = new ContactsEstate(true, false, false, true);

                    $entity = new Estate(
                        $id,
                        $estete->title,
                        $description,
                        $address,
                        $gallery,
                        $contacts,
                        $details,
                        $rent,
                        $terms,
                        $estete->id,
                        self::PREFIX);

                    $entity->save();
                }
            }

        } catch (Exception $e) {
            error_log($e->getMessage(), null, $e->getTraceAsString(), $e->getFile());
        }
    }

    /**
     * @param $estate
     * @return AddressEstate
     */
    public static function getEstateAddress($estate)
    {
        $street = $estate->address->street.' '.$estate->address->houseNumber;

        return new AddressEstate($street, $estate->address->zip, implode(',', (array)$estate->geoLocation));
    }

    /**
     * @param array $images
     * @return GalleryEstate
     */
    public static function getEstateGallery(array $images)
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
     * @param $estate
     * @return RentEstate
     */
    public static function getEstateRent($estate)
    {
        $html = self::getEstateHtml($estate->id);

        $price     = self::toFloat(CrawlHelper::getTableValue($html, 'Kaltmiete'));
        $addtional = self::toFloat(CrawlHelper::getTableValue($html, 'Nebenkosten'));
        $depoist   = self::toFloat(CrawlHelper::getTableValue($html, 'Kaution'));

        return new RentEstate($price, $addtional, $depoist);
    }

    /**
     * @param $estate
     * @return DetailsEstate
     */
    public static function getEstateDetails($estate)
    {
        $html    = self::getEstateHtml($estate->id);
        $heating = self::toFloat(CrawlHelper::getTableValue($html, 'Verbrauchswert'));
        $date    = CrawlHelper::getTableValue($html, 'Verfügbar ab');

        return new DetailsEstate($date, $estate->area, $estate->rooms, $heating);
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
     * @param string $estate_id
     * @return string
     */
    public static function getEstateDescription($estate_id)
    {
        $html = self::getEstateHtml($estate_id);

        $startDivTag = 'object-detail__description';

        $startDiv = strpos($html, $startDivTag);
        $start    = strpos($html, '<p>', $startDiv) + 3;
        $end      = strpos($html, '</p>', $start);

        return substr($html, $start, $end - $start);
    }

    /**
     * @param $estate
     * @return TermEstate
     */
    public static function getEstateTerms($estate)
    {
        $html  = self::getEstateHtml($estate->id);
        $terms = new TermEstate();

        $terms->add('iwp_features', self::getFeatures($html));
        $terms->add('iwp_heatingtype', self::getHeating($html));
        $terms->add('iwp_status', strtolower($estate->commercializationType));

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
     * @return false|string
     */
    public static function getEstateHtml($crawl_id)
    {
        return file_get_contents('https://www.deutsche-wohnen.com/expose/object/'.$crawl_id);
    }
}
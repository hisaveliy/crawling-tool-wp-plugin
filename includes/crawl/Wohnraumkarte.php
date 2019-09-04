<?php


namespace Crawling_WP;


//include_once './autoload.php';

use Exception;

class Wohnraumkarte extends BaseWebsite
{

    const PREFIX = 'wohnraumkarte';

    /**
     * @var ProxyService
     */
    private $proxyService;

    /**
     * Wohnraumkarte constructor.
     * @param ProxyService|null $proxy
     */
    public function __construct(ProxyService $proxy = null)
    {
        $this->proxyService = $proxy;
    }

    /**
     * @param $crawl_id
     * @return bool|string
     * @throws Exception
     */
    public function getEstateHtml($crawl_id)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->getUrl());
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getBody($crawl_id));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($this->proxyService) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxyService->getProxyString());
        }

        $result = curl_exec($ch);

        if (curl_error($ch) !== "") {
            throw new Exception(curl_error($ch));
        };

        curl_close($ch);

        return $result;
    }

    public function updateEstates()
    {
        $paginator = new WohnraumkartePaginator($this->proxyService);

        try {
            $list = $paginator->getEstates(25);
            $old  = CrawlHelper::getListToDrafting($list, self::PREFIX);

            if (! empty($old)) {
                CrawlHelper::draftList($old);
            }

            foreach ($list as $estate) {
                $id   = CrawlHelper::isEstateExist($estate->id, self::PREFIX);
                $html = $this->getEstateHtml($estate->id);

                if ($id) {
                    if (intval(self::toInt(RentEstate::getMonthlyPrice($id))) !== intval($estate->price)) {
                        self::getEstateRent($html)->save($id);
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
     * @return Estate
     * @throws Exception
     */
    public function addEstate($estate)
    {
        $html = $this->getEstateHtml($estate->id);

        $address     = self::getEstateAddress($html);
        $images      = self::getImages($html);
        $gallery     = self::getEstateGallery($images);
        $rent        = self::getEstateRent($html);
        $details     = self::getEstateDetails($html);
        $description = self::getEstateDescription($html);
        $terms       = self::getEstateTerms($html);
        $contacts    = self::getEstateContacts($html);

        $entity = new Estate(
            false,
            self::getTitle($html),
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
     * @return string
     */
    protected function getUrl()
    {
        return 'https://www.wohnraumkarte.de//api/GetRealEstateDetails';
    }

    /**
     * @param $crawl_id
     * @return string
     */
    protected function getBody($crawl_id)
    {
        return http_build_query([
            'id'          => $crawl_id,
            'wrkSettings' => '{"inputPlaceholder":"Stadt, PLZ, Strasse, Objnr.-Extern","logoPath":"","realEstateTypes":{"zimmer":false,"wohnung":true,"haus":true,"grundstueck":false,"einzelhandel":true,"parken":true,"zinshaus_renditeobjekt":true,"buero_praxen":true,"gastgewerbe":false,"hallen_lager_prod":true,"sonstige":false},"accountID":916,"filterOptions":{"balkon":true,"zimmer":false,"wbs":true,"wg_geeignet":false,"haustiere":false,"barrierefrei":true},"callbackLink":"http://your-url.com/northEast/{NE}/southWest/{SW}","northEast":null,"southWest":null,"countObjects":true,"baseURL":"/zuhause-finden/","seoURL":true,"wrkID":null,"showAddrSimObj":false,"showListView":true,"cutTitleAtCharNo":30,"delimiterSymbol":"-","listViewOrderBy":"dist_asc","listViewCurrentPage":1,"resetViewCurrentPage":true,"clockText":"Besichtigung vereinbaren","showInfoWindowBottom":true,"activateFavorites":true,"cookieValue":"8e8b3aeadf76f1027fea99d69b784b972aaebd4a","showOnlyFavorites":false,"showFavFilterMenu":true,"showSocialShare":true,"detailViewVersion":2,"geoLocality":null,"showStandardLoading":false,"showExtendedLoading":false,"exposeBtnBelowDescription":true,"contactBlockAtTop":true,"showChatbotKupoBlock":true,"showRoomFilterDropown":true,"showFavHeadText":true,"showFavHeartWithLabel":true,"showExtendedVisitBtns":true,"showExtendedTopMenu":true,"showWhatsAppBtn":true}'
        ]);
    }

    /**
     * @param $html
     * @return AddressEstate
     */
    public static function getEstateAddress($html): AddressEstate
    {
        $address = CrawlHelper::getContentByAttribute($html, 'wrk_address-info-td');

        if (! empty($address)) {
            $address = trim($address[0]);
        }

        $address = explode(', ', $address);

        $address_info = explode(' ', $address[1]);

        $coords = self::getCoordinates($html);

        return new AddressEstate($address[0], $address_info[0], $coords);
    }

    /**
     * @param $html
     * @return string
     */
    public static function getCoordinates($html)
    {
        $lat = CrawlHelper::getJSVariable($html, 'insLat');
        $lon = CrawlHelper::getJSVariable($html, 'insLon');

        return $lat.','.$lon;
    }

    /**
     * @param $html
     * @return array
     */
    public static function getImages($html)
    {
        if ($c = preg_match_all("/.*?(insertions)(\\/).*?((?:[a-z0-9_]*))(\\.)(jpg)/is", $html, $matches)) {
            $title  = self::getTitle($html);
            $images = [];

            for ($i = 0; $i < count($matches[3]); $i++) {
                $images[] = (object)[
                    'path'  => $matches[3][$i],
                    'title' => $title
                ];
            }

            return $images;
        }

        return [];
    }

    /**
     * @param $html
     * @return string
     */
    public static function getTitle($html)
    {
        if ($c = preg_match_all("/.*?(<h1>)((?:[a-z][a-z0-9_ ]*)).*?(<\\/h1>)/is", $html, $matches)) {
            return $matches[2][0];
        }

        return '';
    }

    public static function getEstateGallery(array $images): GalleryEstate
    {
        $gallery = new GalleryEstate();

        foreach ($images as $i => $img) {
            $gallery->addImage(
                'https://cdn.wohnraumkarte.com/insertions/'.$img->path.'.jpg',
                $img->title.' '.$i,
                ''
            );
        }

        return $gallery;
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
     * @return RentEstate
     */
    public static function getEstateRent($html): RentEstate
    {
        $monthly  = self::toFloat(CrawlHelper::getTableValueTd($html, 'Kaltmiete'));
        $addition = self::toFloat(CrawlHelper::getTableValueTd($html, 'Nebenkosten'));
        $deposit  = self::toFloat(CrawlHelper::getTableValueTd($html, 'Kaution'));

        return new RentEstate($monthly, $addition, $deposit);
    }

    /**
     * @param $estate
     * @return DetailsEstate
     */
    public static function getEstateDetails($estate): DetailsEstate
    {
        $heating = self::toFloat(CrawlHelper::getTableValueTd($estate, 'Endenergiebedarf'));
        $date    = CrawlHelper::getTableValueTd($estate, 'verfügbar ab');
        $area    = self::toFloat(CrawlHelper::getTableValueTd($estate, 'Wohnfläche'));
        $rooms   = CrawlHelper::getTableValueTd($estate, 'Zimmer');

        return new DetailsEstate($date, $area, $rooms, $heating);
    }

    /**
     * @param $html
     * @return string
     */
    public static function getEstateDescription($html)
    {
        $startTag = 'wrk_description-text">';

        $start = strpos($html, $startTag) + strlen($startTag);
        $end   = strpos($html, '</span>', $start);

        return trim(substr($html, $start, $end - $start));
    }

    /**
     * @param $html
     * @return TermEstate
     */
    public static function getEstateTerms($html): TermEstate
    {
        $terms = new TermEstate();

        $terms->add('iwp_type', self::getType($html));
        $terms->add('iwp_heatingtype', self::getHeating($html));
        $terms->add('iwp_status', 'rent');

        return $terms;
    }

    /**
     * @param $html
     * @return ContactsEstate
     */
    public static function getEstateContacts($html): ContactsEstate
    {
        $name  = CrawlHelper::getTableValueTd($html, 'Kontaktperson');
        $phone = CrawlHelper::getTableValueTd($html, 'Telefon');
        $email = CrawlHelper::getTableValueTd($html, 'Email');

        return new ContactsEstate(true, (bool)$name, (bool)$phone, (bool)$email);
    }

    /**
     * @param $html
     * @return string
     */
    protected static function getHeating($html)
    {
        return trim(CrawlHelper::getTableValueTd($html, 'Energieträger'));
    }

    /**
     * @param $html
     * @return bool|string
     */
    protected static function getType($html)
    {
        $types = CrawlHelper::getContentByAttribute($html, 'wrk_art-info-td');

        if (empty($types)) {
            return false;
        }

        return trim(explode('/', $types[0])[0]);
    }

    /**
     * @param $float
     * @return int
     */
    public static function toInt($float)
    {
        return intval(round($float, 0));
    }
}
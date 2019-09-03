<?php


namespace Crawling_WP;


class WohnraumkartePaginator
{

    /**
     * @return bool|string
     * @throws \Exception
     */
    public function getHtml($page = 1)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->getUrl());
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getBody($page));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        if (curl_error($ch) !== "") {
            throw new \Exception(curl_error($ch));
        };

        curl_close($ch);

        return $result;
    }

    /**
     * @param bool $max
     * @return array
     * @throws \Exception
     */
    public function getEstates($max = false)
    {
        $page    = 1;
        $estates = [];

        while (true) {
            try {
                $current = $this->getEstateFrom($this->getHtml($page));
            } catch (\Exception $e) {
                error_log($e->getMessage(), null, $e->getTraceAsString(), $e->getFile());
                $current = [];
            }

            if (empty($current)) {
                break;
            }

            $estates = array_merge($estates, $current);
            $page++;

            if ($max && count($estates) >= $max) {
                break;
            }
        }

        return $estates;
    }

    /**
     * @param $html
     * @return array
     */
    protected function getEstateFrom($html)
    {
        $result = [];

        $ids    = $this->getObjectID($html);
        $prices = $this->getPrices($html);

        if (empty($ids) || count($prices) !== count($ids)) {
            return [];
        }

        for ($i = 0; $i < count($ids); $i++) {
            $result[] = (object)[
                'object_id' => $ids[$i],
                'price'     => $prices[$i]
            ];
        }

        return $result;
    }

    /**
     * @param $html
     * @return array
     */
    protected function getObjectID($html)
    {
        if ($c = preg_match_all("/.*?(objectid)(=)(\")(\\d+)(\")/is", $html, $matches)) {
            return $matches[4];
        }

        return [];
    }

    /**
     * @param $html
     * @return array|mixed
     */
    protected function getPrices($html)
    {
        if ($c = preg_match_all("/.*?(\"wrk_list-item-price\").*?(\\d+).*?(euro)/is", $html, $matches)) {
            return $matches[2];
        }

        return [];
    }

    /**
     * @return string
     */
    protected function getUrl()
    {
        return 'https://www.wohnraumkarte.de//Api/GetRealEstates';
    }

    /**
     * @return string
     */
    protected function getBody($page = 1)
    {
        return http_build_query([
            'bounds'           => '((42.04537101657428, -4.614572859375016), (59.547095520406245, 25.509938859374984))',
            'zoom'             => 4,
            'type_id'          => 0,
            'rent_type_rent'   => 1,
            'rent_type_buy'    => 0,
            'max_rent'         => 99999,
            'min_rent'         => 0,
            'max_size'         => 9999,
            'min_size'         => 1,
            'available_from'   => 'egal',
            'available_to'     => 'egal',
            'days_date_range'  => 14,
            'renting_period'   => 'egal',
            'room_count'       => 'egal',
            'wbs'              => 'egal',
            'wg_geeignet'      => 'undefined',
            'balkon'           => 'egal',
            'haustiere'        => 'undefined',
            'barrierefrei'     => 'egal',
            'courtage'         => 'egal',
            'returnNoClusters' => true,
            'wrkSettings'      => '{"inputPlaceholder":"Stadt, PLZ, Strasse, Objnr.-Extern","logoPath":"","realEstateTypes":{"zimmer":false,"wohnung":true,"haus":true,"grundstueck":false,"einzelhandel":true,"parken":true,"zinshaus_renditeobjekt":true,"buero_praxen":true,"gastgewerbe":false,"hallen_lager_prod":true,"sonstige":false},"accountID":916,"filterOptions":{"balkon":true,"zimmer":false,"wbs":true,"wg_geeignet":false,"haustiere":false,"barrierefrei":true},"callbackLink":"http://your-url.com/northEast/{NE}/southWest/{SW}","northEast":null,"southWest":null,"countObjects":true,"baseURL":"/zuhause-finden/","seoURL":true,"wrkID":null,"showAddrSimObj":false,"showListView":true,"cutTitleAtCharNo":30,"delimiterSymbol":"-","listViewOrderBy":"dist_asc","listViewCurrentPage":'.$page.',"resetViewCurrentPage":true,"clockText":"Besichtigung vereinbaren","showInfoWindowBottom":true,"activateFavorites":true,"cookieValue":"8e8b3aeadf76f1027fea99d69b784b972aaebd4a","showOnlyFavorites":false,"showFavFilterMenu":true,"showSocialShare":true,"detailViewVersion":2,"geoLocality":null,"showStandardLoading":false,"showExtendedLoading":false,"exposeBtnBelowDescription":true,"contactBlockAtTop":true,"showChatbotKupoBlock":true,"showRoomFilterDropown":true,"showFavHeadText":true,"showFavHeartWithLabel":true,"showExtendedVisitBtns":true,"showExtendedTopMenu":true,"showWhatsAppBtn":true}'
        ]);
    }
}

//$e = new WohnraumkartePaginator();
//$e->getEstates();
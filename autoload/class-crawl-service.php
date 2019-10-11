<?php

namespace Crawling_WP;


use Exception;

class CrawlService
{

    /**
     * @return array
     */
    public static function getDataFromApi()
    {
        $url = self::getUrl();

        if (empty($url)) {
            return [];
        }

        try {
            $data = file_get_contents($url.'/estates');

            return json_decode($data);
        } catch (Exception $e) {
            return [];
        }
    }

    protected static function getUrl()
    {
        return get_option(PREFIX.'_api_url');
    }

    public static function updateOption($key, $value)
    {
        if (empty(self::getUrl())) {
            return;
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, self::getUrl().'/options');
        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(compact('key', 'value')));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);
    }

    public static function getOptions()
    {
        return [
            'proxy_api_key',
            'gmap_api_key'
        ];
    }
}

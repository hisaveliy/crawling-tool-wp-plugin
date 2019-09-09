<?php


namespace Crawling_WP;


use Exception;

class CrawlHelper
{

    /**
     * @param string $html
     * @param string $attribute
     * @return array
     */
    public static function getContentByAttribute($html, $attribute)
    {
        $result = [];
        $start  = 0;

        while (strpos($html, $attribute, $start) !== false) {
            $s = strpos($html, $attribute, $start) + strlen($attribute);

            $tag_start = strpos($html, '>', $s) + 1;

            $tag_end = strpos($html, '</', $tag_start);

            $result[] = substr($html, $tag_start, $tag_end - $tag_start);

            $start = $tag_end;
        }

        return $result;
    }

    /**
     * @param string $html
     * @param string $name
     * @return string
     */
    public static function getTableValue($html, $name)
    {
        $startTag = '<td>'.$name.'</td> <td>';

        $start = strpos($html, $startTag) + strlen($startTag);
        if ($start === strlen($startTag)) {
            return '';
        }
        $end = strpos($html, '</td>', $start);

        return substr($html, $start, $end - $start);
    }

    /**
     * @param $html
     * @param $th
     * @return string
     */
    public static function getTableValueTd($html, $th)
    {
        $startTag = '<th>'.$th.'</th>';

        $start = strpos($html, $startTag) + strlen($startTag);
        if ($start === strlen($startTag)) {
            return false;
        }
        $start = strpos($html, '<td', $start) + 3;
        $start = strpos($html, '>', $start) + 1;

        $end = strpos($html, '</td>', $start);

        return substr($html, $start, $end - $start);
    }

    /**
     * @param $html
     * @param $var
     * @return bool
     */
    public static function getJSVariable($html, $var)
    {
        if ($c = preg_match_all("/.*?({$var}).*?(=).*?([+-]?\\d*\\.\\d+)(?![-+0-9\\.])(;)/is", $html, $matches)) {
            return $matches[3][0];
        }

        return null;
    }

    /**
     * @param $estate_id
     * @param $class
     * @return bool|int
     */
    public static function isEstateExist($estate_id, $class)
    {
        global $wpdb;

        $result = $wpdb->get_results(
            "SELECT *
                    FROM `{$wpdb->prefix}postmeta` as a
                             INNER JOIN `{$wpdb->prefix}postmeta` as b ON a.post_id = b.post_id
                    WHERE a.meta_key = '_crawl_id' AND a.meta_value = '{$estate_id}' 
                    AND b.meta_key = '_crawl_class' AND b.meta_value = '{$class}'", ARRAY_A);

        if (empty($result) || ! array_key_exists('post_id', $result[0])) {
            return false;
        }

        return $result[0]['post_id'];
    }

    /**
     * @param array $estates
     * @param $class
     * @return array
     */
    public static function getListToDrafting(array $estates, $class)
    {
        if (empty($estates)) {
            return [];
        }
        global $wpdb;

        $ids = implode(', ', array_map(function ($estate) {
            return "'{$estate->id}'";
        }, $estates));


        $query = "SELECT a.post_id
                    FROM `{$wpdb->prefix}postmeta` as a
                             INNER JOIN `{$wpdb->prefix}postmeta` as b ON a.post_id = b.post_id
                    WHERE a.meta_key = '_crawl_id' AND a.meta_value NOT IN ({$ids})
                    AND b.meta_key = '_crawl_class' AND b.meta_value = '{$class}'";

        $result = $wpdb->get_results($query, ARRAY_N);

        if (empty($result)) {
            return [];
        }

        return $result[0];
    }

    /**
     * @param array $ids
     * @return bool|int
     */
    public static function draftList(array $ids)
    {
        global $wpdb;

        $ids = implode(', ', array_map(function ($id) {
            return "'{$id}'";
        }, $ids));

        $query = "UPDATE `{$wpdb->prefix}posts` SET post_status = 'draft' WHERE ID IN ({$ids})";

        return $wpdb->query($query);
    }

    /**
     * @param $prefix
     * @return bool|string
     */
    public static function getCrawlClass($prefix)
    {
        $sites = [
            DeutscheWohnen::PREFIX => DeutscheWohnen::class,
            Wohnraumkarte::PREFIX  => Wohnraumkarte::class
        ];

        if (array_key_exists($prefix, $sites)) {
            return $sites[$prefix];
        }

        return false;
    }

    /**
     * @return ProxyService|null|ProxyCacheService
     */
    public static function getProxyService($cache = true)
    {
        $key = get_option(PREFIX.'_proxy_api_key') ?? false;

        try {
            if ($cache) {
                return new ProxyCacheService();
            } else {
                return new ProxyService($key);
            }
        } catch (Exception $e) {
            return null;
        }
    }


}
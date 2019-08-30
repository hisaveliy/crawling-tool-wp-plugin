<?php


namespace Crawling_WP;


class CrawlHelper
{

    /**
     * @param string $html
     * @param string $attribute
     * @return array
     */
    public static function getContentByClassName($html, $attribute)
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

        $end = strpos($html, '</td>', $start);

        return substr($html, $start, $end - $start);
    }
}
<?php


namespace Crawling_WP;


/**
 * Class DetailsEstate
 * @package Crawling_WP
 */
class DetailsEstate
{

    private $date;
    private $area;
    private $rooms;
    private $heating;

    /**
     * DetailsEstate constructor.
     * @param $date
     * @param $area
     * @param $rooms
     * @param $heating
     */
    public function __construct($date, $area, $rooms, $heating)
    {
        $this->date    = strtotime($date);
        $this->area    = $area;
        $this->rooms   = $rooms;
        $this->heating = $heating;
    }

    /**
     * @param $post_id
     */
    public function save($post_id)
    {
        update_post_meta($post_id, '_iwp_takeoverdate', $this->date);
        update_post_meta($post_id, '_iwp_area_size', $this->area);
        update_post_meta($post_id, '_iwp_bedrooms', $this->rooms);
        update_post_meta($post_id, '_iwp_heatingusage', $this->heating);
    }

    /**
     * @param $rooms
     * @return $this
     */
    public function setRooms($rooms)
    {
        $this->rooms = $rooms;

        return $this;
    }

    /**
     * @param $area
     * @return $this
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }
}
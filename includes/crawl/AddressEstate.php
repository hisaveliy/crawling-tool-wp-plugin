<?php


namespace Crawling_WP;


class AddressEstate
{

    private $address;
    private $postCode;
    private $coordinates;

    /**
     * AddressEstate constructor.
     * @param $address
     * @param $postCode
     * @param $coordinates
     */
    public function __construct($address, $postCode, $coordinates)
    {
        $this->address     = $address;
        $this->postCode    = $postCode;
        $this->coordinates = $coordinates;
    }

    public function save($post_id)
    {
        update_post_meta($post_id, '_iwp_address', $this->address);
        update_post_meta($post_id, '_iwp_zip', $this->postCode);
        update_post_meta($post_id, '_iwp_map', $this->coordinates);
    }
}
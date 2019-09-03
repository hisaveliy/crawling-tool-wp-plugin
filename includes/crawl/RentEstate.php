<?php


namespace Crawling_WP;


class RentEstate
{

    private $rent_monthly;
    private $addition_cost;
    private $deposit;

    /**
     * RentEstate constructor.
     * @param float $rent_monthly
     * @param float $addition_cost
     * @param float $deposit
     */
    public function __construct($rent_monthly, $addition_cost, $deposit)
    {
        $this->rent_monthly  = $rent_monthly;
        $this->addition_cost = $addition_cost;
        $this->deposit       = $deposit;
    }

    /**
     * @param $post_id
     */
    public function save($post_id)
    {
        update_post_meta($post_id, '_iwp_price', $this->rent_monthly);
        update_post_meta($post_id, '_iwp_utilities', $this->addition_cost);
        update_post_meta($post_id, '_iwp_deposit', $this->deposit);
    }

    /**
     * @param $post_id
     * @return float
     */
    public static function getTotalRent($post_id)
    {
        $addition = get_post_meta($post_id, '_iwp_utilities', true);

        return self::getMonthlyPrice($post_id) + $addition;
    }

    /**
     * @param $post_id
     * @return float
     */
    public static function getMonthlyPrice($post_id)
    {
        return get_post_meta($post_id, '_iwp_price', true);
    }
}
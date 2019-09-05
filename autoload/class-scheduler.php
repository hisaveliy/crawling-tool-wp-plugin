<?php

namespace Crawling_WP;


use Exception;
use ReflectionClass;
use ReflectionException;

defined('ABSPATH') || exit;


class Scheduler
{

    const HOOK        = 'crawling_global_schedule';
    const HOOK_ESTATE = 'crawling_estates_schedule';

    function __construct()
    {
        add_filter('cron_schedules', __CLASS__.'::add_schedules');
        add_action(self::HOOK, __CLASS__.'::process_global_schedule');
        add_action(self::HOOK_ESTATE, __CLASS__.'::process_estates_schedule');
    }

    /**
     * The instance of this class
     *
     * @since 1.0.0
     * @var null|object
     */
    protected static $instance = null;

    /**
     * Return an instance of this class.
     *
     * @return    object    A single instance of this class.
     * @since     1.0.0
     *
     */
    public static function instance()
    {
        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public static function addScheduleEvent()
    {
        wp_clear_scheduled_hook(Scheduler::HOOK);
        wp_schedule_event(time(), 'crawling_time', Scheduler::HOOK);
    }

    /**
     * @param array $schedules
     * @return array
     */
    public static function add_schedules(array $schedules)
    {
        $period = get_option(PREFIX.'_scheduler_period');

        if (! $period) {
            $period = 4;
        }

        $schedules['crawling_time'] = [
            'display'  => $period.' day(days)',
            'interval' => DAY_IN_SECONDS * $period
        ];

        return $schedules;
    }

    public static function process_global_schedule()
    {
        self::crawlDeutscheWohnen();
        self::crawlWohnraumkarte();
    }

    /**
     * @param $args
     * @throws ReflectionException
     */
    public static function process_estates_schedule($args)
    {
        if (! array_key_exists('entities', $args) || ! array_key_exists('prefix', $args)) {
            return;
        }

        $crawl_site_name = CrawlHelper::getCrawlClass($args['prefix']);

        if (! $crawl_site_name || ! class_exists($crawl_site_name)) {
            return;
        }

        foreach ($args['entities'] as $entity) {
            $proxy = CrawlHelper::getProxyService();
            $class = new ReflectionClass($crawl_site_name);

            /** @var BaseWebsite $crawl_site */
            $crawl_site = $class->newInstanceArgs([$proxy]);
            $crawl_site->addEstate($entity);

            unset($crawl_site);
            unset($class);
        }
    }

    protected static function crawlDeutscheWohnen()
    {
        try {
            $proxy       = CrawlHelper::getProxyService();
            $site        = new DeutscheWohnen($proxy);
            $list        = json_decode($site->getHtml());
            $new_estates = [];

            $old = CrawlHelper::getListToDrafting($list, DeutscheWohnen::PREFIX);

            if (! empty($old)) {
                CrawlHelper::draftList($old);
            }

            foreach ($list as $estate) {
                $id = CrawlHelper::isEstateExist($estate->id, DeutscheWohnen::PREFIX);

                if ($id) {
                    if (RentEstate::getTotalRent($id) !== $estate->price) {
                        $html = $site->getEstateHtml($estate->id);
                        DeutscheWohnen::getEstateRent($html)->save($id);
                    }
                    continue;
                } else {
                    $new_estates[] = $estate;
                }
            }

            self::createSingleSchedule($new_estates, DeutscheWohnen::PREFIX);
        } catch (Exception $e) {
            error_log($e->getMessage(), null, $e->getTraceAsString(), $e->getFile());
        }
    }

    protected static function crawlWohnraumkarte()
    {
        try {
            $proxy = CrawlHelper::getProxyService();
            $site  = new WohnraumkartePaginator($proxy);
            $list  = $site->getEstates();
            $old   = CrawlHelper::getListToDrafting($list, Wohnraumkarte::PREFIX);

            $new_estates = [];

            if (! empty($old)) {
                CrawlHelper::draftList($old);
            }

            foreach ($list as $estate) {
                $id = CrawlHelper::isEstateExist($estate->id, Wohnraumkarte::PREFIX);

                if ($id) {
                    if (intval(Wohnraumkarte::toInt(RentEstate::getMonthlyPrice($id))) !== intval($estate->price)) {
                        $site = new Wohnraumkarte(CrawlHelper::getProxyService());
                        $html = $site->getEstateHtml($estate->id);
                        Wohnraumkarte::getEstateRent($html)->save($id);
                    }

                    continue;
                } else {
                    $new_estates[] = $estate;
                }
            }

            self::createSingleSchedule($new_estates, Wohnraumkarte::PREFIX);
        } catch (Exception $e) {
            error_log($e->getMessage(), null, $e->getTraceAsString(), $e->getFile());
        }
    }

    /**
     * @param $entities
     * @param $prefix
     */
    protected static function createSingleSchedule($entities, $prefix)
    {
        $delay = rand(60, 200);

        while (wp_schedule_single_event(time() + $delay, self::HOOK_ESTATE, compact('entities', 'prefix')) === false) {
            $delay += $delay;
        }
    }
}
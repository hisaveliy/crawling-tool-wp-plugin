<?php

namespace Crawling_WP;


use Exception;
use ReflectionClass;
use ReflectionException;

defined('ABSPATH') || exit;


class SchedulerOld
{

    const HOOK        = 'crawling_global_schedule';
    const HOOK_ESTATE = 'crawling_estates_schedule';
    const HOOK_PROXY  = 'crawling_proxy_schedule';


    const PIECES_SIZE = 15;

    function __construct()
    {
        add_filter('cron_schedules', __CLASS__.'::add_schedules');
        add_action(self::HOOK, __CLASS__.'::process_global_schedule');
        add_action(self::HOOK_ESTATE, __CLASS__.'::process_estates_schedule', 10, 2);
        add_action(self::HOOK_PROXY, __CLASS__.'::crawling_proxy_schedule');
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
        wp_clear_scheduled_hook(Scheduler::HOOK_PROXY);
        wp_schedule_event(time(), 'proxy_time', Scheduler::HOOK_PROXY);

        wp_clear_scheduled_hook(Scheduler::HOOK);
        wp_schedule_event(time() + 60 * 15, 'crawling_time', Scheduler::HOOK);
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

        $schedules['proxy_time'] = [
            'display'  => ProxyService::CACHE_TIME.' min',
            'interval' => ProxyService::CACHE_TIME * 60
        ];

        return $schedules;
    }

    public static function process_global_schedule()
    {
        self::crawlDeutscheWohnen();
        self::crawlWohnraumkarte();
    }

    /**
     * @param $entities
     * @param $prefix
     * @throws ReflectionException
     */
    public static function process_estates_schedule($entities, $prefix)
    {
        $crawl_site_name = CrawlHelper::getCrawlClass($prefix);

        if (! $crawl_site_name || ! class_exists($crawl_site_name)) {
            return;
        }

        foreach ($entities as $entity) {
            $proxy = CrawlHelper::getProxyService();
            $class = new ReflectionClass($crawl_site_name);

            /** @var BaseWebsite $crawl_site */
            $crawl_site = $class->newInstanceArgs([$proxy]);
            $estate     = $crawl_site->addEstate($entity);

            if ($estate === null) {
                self::createSingleSchedule([$entity], $prefix);
            }

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

            $pieces = array_chunk($new_estates, self::PIECES_SIZE);
            foreach ($pieces as $piece) {
                self::createSingleSchedule($piece, DeutscheWohnen::PREFIX);
            }
        } catch (Exception $e) {
            error_log($e->getMessage(), null, $e->getTraceAsString(), $e->getFile());
        }
    }

    protected static function crawlWohnraumkarte()
    {
        try {
            $site = new WohnraumkartePaginator();
            $list = $site->getEstates();
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

            $pieces = array_chunk($new_estates, self::PIECES_SIZE);
            foreach ($pieces as $piece) {
                self::createSingleSchedule($piece, Wohnraumkarte::PREFIX);
            }
        } catch (Exception $e) {
            error_log($e->getMessage(), null, $e->getTraceAsString(), $e->getFile());
        }
    }

    public static function crawling_proxy_schedule()
    {
        $proxies = [];

        for ($i = 0; $i < ProxyService::CACHE_TIME * 2; $i++) {
            $proxy = CrawlHelper::getProxyService(false);

            $proxies[] = [
                'proxy' => $proxy->getProxyString(),
                'type'  => $proxy->getCurlProxyType()
            ];
        }

        set_transient(ProxyService::PROXY_CACHE, $proxies, ProxyService::CACHE_TIME * 60 * 2);
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
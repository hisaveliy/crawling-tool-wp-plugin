<?php

namespace Crawling_WP;


defined('ABSPATH') || exit;


class Scheduler
{

    const HOOK        = 'crawling_global_schedule';
    const HOOK_IMAGES = 'crawling_image_schedule';


    const PIECES_SIZE = 15;

    function __construct()
    {
        add_filter('cron_schedules', __CLASS__.'::add_schedules');
        add_action(self::HOOK, __CLASS__.'::process_global_schedule');
        add_action(self::HOOK_IMAGES, __CLASS__.'::process_images_schedule');
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

        return $schedules;
    }

    public static function process_global_schedule()
    {
        $entities = CrawlService::getDataFromApi();

        foreach ($entities as $estate) {
            $id  = CrawlHelper::isEstateExist($estate->crawl_id, $estate->crawl_class);
            $new = false;

            if (! $id) {
                $new = true;

                $id = wp_insert_post([
                    'post_title'   => $estate->title,
                    'post_content' => $estate->description,
                    'post_author'  => 1,
                    'post_type'    => 'iwp_property',
                ]);
            }

            if (GalleryEstate::isImageUpdated($id, $estate->attachment)) {
                update_post_meta($id, '_crawling_attachments', $estate->attachment);

                self::createSingleSchedule($id, self::HOOK_IMAGES);
            }


            $eTerm = new TermEstate();

            foreach ($estate->term as $term) {
                $eTerm->add($term->taxonomy, unserialize($term->term));
            }
            $eTerm->save($id);

            foreach ($estate->meta as $meta) {
                update_post_meta($id, $meta->meta_key, $meta->meta_value);
            }

            wp_update_post([
                'ID'           => $id,
                'post_title'   => $estate->title,
                'post_content' => $estate->description,
                'post_status'  => $new ? 'pending' : $estate->status
            ]);
        }
    }

    /**
     * @param $post_id
     */
    public static function process_images_schedule($post_id)
    {
        $attachments = get_post_meta($post_id, '_crawling_attachments', true);

        if (! $attachments) {
            return;
        }

        $gallery = new GalleryEstate(CrawlHelper::getProxyService(false));

        if ($attachments) {
            foreach ($attachments as $img) {
                $gallery->addImage($img->url, $img->title, $img->description);
            }
        }

        $gallery->save($post_id);

        wp_update_post([
            'ID'          => $post_id,
            'post_status' => 'publish'
        ]);
    }


    /**
     * @param $post_id
     * @param $hook
     */
    protected static function createSingleSchedule($post_id, $hook)
    {
        $delay = rand(10, 60);

        while (wp_schedule_single_event(time() + $delay, $hook, compact('post_id')) === false) {
            $delay += $delay;
        }
    }
}

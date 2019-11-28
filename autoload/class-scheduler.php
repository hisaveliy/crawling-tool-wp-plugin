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

        add_action('before_delete_post', __CLASS__.'::remove_gallery', 999);
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
            'interval' => DAY_IN_SECONDS * doubleval($period)
        ];

        return $schedules;
    }

    public static function process_global_schedule()
    {
        $entities = CrawlService::getDataFromApi();

        self::removeOldEstates($entities);

        foreach ($entities as $estate) {
            $id  = CrawlHelper::isEstateExist($estate->crawl_id, $estate->crawl_class);
            $new = false;

            if ($id && get_post_meta($id, '_crawl_desync', true) === 'on') {
                continue;
            }

            if (! $id) {
                $new = true;

                $id = wp_insert_post([
                    'post_title'   => $estate->title,
                    'post_content' => $estate->description,
                    'post_author'  => 1,
                    'post_type'    => 'iwp_property',
                    'post_status'  => 'pending'
                ]);

                update_post_meta($id, '_crawl_id', $estate->crawl_id);
                update_post_meta($id, '_crawl_class', $estate->crawl_class);
                update_post_meta($id, '_iwp_featured', true);

            }
            update_post_meta($id, '_iwp_property_id', $id);

            //            if (GalleryEstate::isImageUpdated($id, $estate->attachment) || $new) {
            update_post_meta($id, '_crawling_attachments', $estate->attachment);
//            }

            if (! empty($estate->attachment)) {
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
     * @param array $list
     */
    public static function removeOldEstates($list)
    {
        $old = self::getListToDrafting($list);

//        global $wpdb;
//
//        $list = implode(', ', array_map(function ($item) {
//            return "'{$item->post_id}'";
//        }, $old));

        foreach ($old as $item) {
            if (get_post_meta($item->post_id, '_crawl_desync', true) === 'on'){
                continue;
            }

            wp_delete_post($item->post_id, true);
        }

//        $wpdb->query("UPDATE `{$wpdb->prefix}posts` SET post_status='trash' WHERE ID IN ({$list})");
    }

    /**
     * @param array $list
     * @param string $class
     * @return array
     */
    public static function getListToDrafting($list, $class = false)
    {
        $list = implode(', ', array_map(function ($item) {
            return "'{$item->crawl_id}'";
        }, $list));

        global $wpdb;

        if ($class) {
            $result = $wpdb->get_results("SELECT id.post_id FROM `{$wpdb->prefix}postmeta` AS class
            INNER JOIN `{$wpdb->prefix}postmeta` AS id ON id.post_id = class.post_id
             WHERE class.meta_key = '_crawl_class' AND class.meta_value = '{$class}'
             AND id.meta_key = '_crawl_id' AND id.meta_value NOT IN ({$list})");
        } else {
            $result = $wpdb->get_results("SELECT id.post_id FROM `{$wpdb->prefix}postmeta` AS class
            INNER JOIN `{$wpdb->prefix}postmeta` AS id ON id.post_id = class.post_id
             WHERE class.meta_key = '_crawl_class'
             AND id.meta_key = '_crawl_id' AND id.meta_value NOT IN ({$list})");
        }


        return $result;
    }

    /**
     * @param $post_id
     */
    public static function process_images_schedule($post_id)
    {
        if (get_post_status($post_id) === false) {
            var_dump($post_id.'post_not found');

            return;
        }

        $attachments = get_post_meta($post_id, '_crawling_attachments', true);

        if (! $attachments) {
            var_dump($post_id.'no attachments');

            return;
        }

        $gallery = new GalleryEstate();

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

    public static function remove_gallery($post_id)
    {
        global $post_type;

        if ($post_type !== 'iwp_property') {
            return;
        }

        $thumbnail   = get_post_meta($post_id, '_thumbnail_id');
        $cover_image = get_post_meta($post_id, '_iwp_cover_image');
        $gallery     = get_post_meta($post_id, '_thumbnail_id');

        foreach (array_merge($thumbnail, $cover_image, $gallery) as $img) {
            wp_delete_attachment($img, true);
        }

        return;
    }
}

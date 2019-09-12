<?php


namespace Crawling_WP;


use WP_Error;

class GalleryEstate
{

    /**
     * @var array
     */
    private $images = [];

    /**
     * @param $post_id
     */
    public function save($post_id)
    {
        foreach ($this->images as $image) {
//            $attachment = get_page_by_title($image['title'], OBJECT, 'attachment');
//
//            if ($attachment) {
//                continue;
//            }

            $filename = wp_upload_dir()['path'].'/'.uniqid().'.'.pathinfo($image['url'])['extension'];
            $result   = file_put_contents($filename, file_get_contents($image['url']));

            if (! $result) {
                continue;
            }

            $filetype = wp_check_filetype(basename($filename), null);

            $wp_upload_dir = wp_upload_dir();

            $attachment = array(
                'guid'           => $wp_upload_dir['url'].'/'.basename($filename),
                'post_mime_type' => $filetype['type'],
                'post_title'     => preg_replace('/\.[^.]+$/', '', basename($filename)),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );

            $attach_id = wp_insert_attachment($attachment, $filename, 0);

            if ($attach_id instanceof WP_Error) {
                continue;
            }

            include_once(ABSPATH.'wp-admin/includes/image.php');

            $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
            wp_update_attachment_metadata($attach_id, $attach_data);

            add_post_meta($post_id, '_iwp_cover_image', $attach_id);
            add_post_meta($post_id, '_thumbnail_id', $attach_id);
            add_post_meta($post_id, '_iwp_gallery', $attach_id);
        }
    }

    /**
     * @param $url
     * @param $title
     * @param $description
     */
    public function addImage($url, $title, $description)
    {
        $this->images[] = compact('url', 'title', 'description');
    }
}
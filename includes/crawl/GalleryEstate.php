<?php


namespace Crawling_WP;


use Exception;
use WP_Error;

class GalleryEstate
{

    /**
     * @var ProxyInterface
     */
    private $proxy;

    /**
     * GalleryEstate constructor.
     * @param ProxyInterface|null $proxy
     */
    public function __construct(ProxyInterface $proxy = null)
    {
        if ($proxy) {
            $this->proxy = $proxy;
        }
    }

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
            $filename = wp_upload_dir()['path'].'/'.uniqid().'.'.pathinfo($image['url'])['extension'];
            $result   = file_put_contents($filename, $this->getImage($image['url']));

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

    /**
     * @param $url
     * @return string
     */
    public function getImage($url)
    {
        $img = null;

        while ($img === null) {
            try {
                $img = CrawlHelper::sendGetRequest($url, $this->proxy);
            } catch (Exception $e) {
                $img = null;
            }
        }

        return $img;
    }

    public static function isImageUpdated($post_id, $images = [])
    {
        $attachments = $attachments = get_post_meta($post_id, '_crawling_attachments', true);;
        if (! $attachments) {
            $attachments = [];
        }

        return crc32(json_encode($attachments)) !== crc32(json_encode($images));
    }
}
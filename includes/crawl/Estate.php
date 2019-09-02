<?php


namespace Crawling_WP;


class Estate
{

    private $post_id;
    private $title;
    private $description;

    /**
     * @var AddressEstate
     */
    private $address;

    /**
     * @var GalleryEstate
     */
    private $gallery;
    private $crawl_id;
    private $crawl_site;

    /**
     * @var ContactsEstate
     */
    private $contacts;

    /**
     * @var DetailsEstate
     */
    private $details;

    /**
     * @var RentEstate
     */
    private $rent;

    /**
     * @var TermEstate
     */
    private $terms;

    /**
     * Estate constructor.
     * @param $post_id
     * @param $title
     * @param $description
     * @param AddressEstate $address
     * @param GalleryEstate $gallery
     * @param ContactsEstate $contacts
     * @param DetailsEstate $details
     * @param RentEstate $rent
     * @param TermEstate $terms
     * @param $crawl_id
     * @param $crawl_site
     */
    public function __construct(
        $post_id,
        $title,
        $description,
        AddressEstate $address,
        GalleryEstate $gallery,
        ContactsEstate $contacts,
        DetailsEstate $details,
        RentEstate $rent,
        TermEstate $terms,
        $crawl_id,
        $crawl_site
    ) {
        $this->post_id     = $post_id;
        $this->title       = $title;
        $this->description = $description;
        $this->address     = $address;
        $this->gallery     = $gallery;
        $this->crawl_id    = $crawl_id;
        $this->crawl_site  = $crawl_site;
        $this->contacts    = $contacts;
        $this->details     = $details;
        $this->rent        = $rent;
        $this->terms       = $terms;
    }

    public function save()
    {
        if (! $this->post_id) {
            $this->post_id = wp_insert_post([
                'post_title'   => $this->title,
                'post_content' => $this->description,
                'post_author'  => 1,
                'post_type'    => 'iwp_property'
            ]);

            update_post_meta($this->post_id, '_crawl_id', $this->crawl_id);
            update_post_meta($this->post_id, '_crawl_class', $this->crawl_site);
        }

        $this->update_meta();
    }

    protected function update_meta()
    {
        $this->contacts->save($this->post_id);
        $this->address->save($this->post_id);
        $this->details->save($this->post_id);
        $this->gallery->save($this->post_id);
        $this->terms->save($this->post_id);
        $this->rent->save($this->post_id);
    }
}
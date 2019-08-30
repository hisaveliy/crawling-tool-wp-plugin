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
     * @param $title
     * @param $description
     * @param AddressEstate $address
     * @param GalleryEstate $gallery
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
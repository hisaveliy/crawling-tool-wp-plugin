<?php


namespace Crawling_WP;


class ContactsEstate
{

    private $messages;
    private $name;
    private $phone;
    private $email;

    /**
     * ContactsEstate constructor.
     * @param bool $messages
     * @param bool $name
     * @param bool $phone
     * @param bool $email
     */
    public function __construct($messages, $name, $phone, $email)
    {
        $this->messages = $messages;
        $this->name     = $name;
        $this->phone    = $phone;
        $this->email    = $email;
    }

    public function save($post_id)
    {
        //TODO TheArdent SET Default
    }
}
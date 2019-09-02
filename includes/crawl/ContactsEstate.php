<?php


namespace Crawling_WP;


class ContactsEstate
{

    private $message;
    private $name;
    private $telephone;
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
        $this->message   = $messages;
        $this->name      = $name;
        $this->telephone = $phone;
        $this->email     = $email;
    }

    /**
     * @param $post_id
     */
    public function save($post_id)
    {
        delete_post_meta($post_id, '_iwp_contact_infomation');

        foreach ($this->getAttributes() as $attribute) {
            if ($this->$attribute) {
                add_post_meta($post_id, '_iwp_contact_infomation', $attribute);
            }
        }
    }

    /**
     * @return array
     */
    protected function getAttributes()
    {
        return [
            'message',
            'name',
            'telephone',
            'email'
        ];
    }
}
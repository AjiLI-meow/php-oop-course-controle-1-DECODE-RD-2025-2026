<?php

namespace App\Entities;

class Contact{
    const CONTACT_ID_FORMAT = '%s_%s.json';
    public string $email;
    public string $message;
    public string $subject;
    private \DateTime $dateOfCreation;
    private \DateTime $dateOfLastUpdate;


    public function __construct()
    {
        $this->dateOfCreation = new \DateTime();
        $this->dateOfLastUpdate = $this->dateOfCreation;
    }
    public function getDateOfCreation(): \DateTime {
        return $this->dateOfCreation;
    }

    public function getDateOfLastUpdate(): \DateTime {
        return $this->dateOfLastUpdate;
    }

    public function getId(): string {
        return sprintf(self::CONTACT_ID_FORMAT, $this->dateOfCreation->format('Y-m-d_h-i-s'), $this->email);
    }

}
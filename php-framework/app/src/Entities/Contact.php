<?php

namespace App\Entities;

class Contact{
    public string $email;
    public string $message;
    public string $subject;
    private int $dateOfCreation;
    private int $dateOfLastUpdate;


    public function __construct()
    {
        $this->dateOfCreation = time();
        $this->dateOfLastUpdate = $this->dateOfCreation;
    }
    public function getDateOfCreation(): \DateTime {
        return $this->dateOfCreation;
    }

    public function getDateOfLastUpdate(): \DateTime {
        return $this->dateOfLastUpdate;
    }

    public function getId(): string {
        return $this->getFormatedTimestamp() . '_' . $this->getSafeEmail() . '.json';
    } // id is filename

    private function getSafeEmail(): string{
        return preg_replace('/[^A-Za-z0-9._@-]/', '_', $this->email); //may put a wrong email address ?
    }

    private function getFormatedTimestamp(): string{
        return date('Y-m-d_H-i-s', $this->dateOfCreation);
    }

}
<?php

namespace App\Entities;

class Contact implements \JsonSerializable{
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

    public function getDateOfCreation(): int {
        return $this->dateOfCreation;
    }

    public function getDateOfLastUpdate(): int {
        return $this->dateOfLastUpdate;
    }

    public function getId(): string {
        return $this->getFormatedTimestamp() . '_' . $this->getSafeEmail();
    } // id is filename without .json

    private function getSafeEmail(): string{
        return preg_replace('/[^A-Za-z0-9._@-]/', '_', $this->email); //may put a wrong email address ?
    }

    private function getFormatedTimestamp(): string{
        return date('Y-m-d_H-i-s', $this->dateOfCreation);
    }

    public function jsonSerialize(): array{
        $vars = get_object_vars($this);
        return $vars;
    }

}
<?php

namespace App\Services;

use App\Entities\Contact;

class ContactService{
    private const CONTACT_DIRECTORY = __DIR__.'/../../var/contacts';

    public function buildContact(array $body): Contact{
        $contact = new Contact();
        $contact->email = (string) $body['email'];
        $contact->subject = (string) $body['subject'];
        $contact->message = (string) $body['message'];

        return $contact;
    }

    public function saveContact(Contact $contact){
        $fileName = $contact->getId();
        file_put_contents(SELF::CONTACT_DIRECTORY.'/'.$fileName, json_encode($contact, JSON_PRETTY_PRINT));
    }

    public function getAllContacts(): array{
        $allContacts = [];
        $files = glob(SELF::CONTACT_DIRECTORY.'/*.json');

        foreach($files as $file){
            $allContacts = array_merge(
                $allContacts,
                json_decode(file_get_contents($file), true)
            );
        }
        return $allContacts;
    }

}
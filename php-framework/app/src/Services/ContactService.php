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
        file_put_contents(SELF::CONTACT_DIRECTORY.'/'.$fileName.'.json', json_encode($contact, JSON_PRETTY_PRINT));
    }

    public function getAllContacts(): array{
        $allContacts = [];
        $files = glob(SELF::CONTACT_DIRECTORY.'/*.json');

        foreach($files as $file){
            $contact = json_decode(file_get_contents($file), true);
            if (is_array($contact)){
                $allContacts[] = $contact;
            }
        }
        return $allContacts;
    }

    public function getContactById(string $id): ?array {
        $filePath = self::CONTACT_DIRECTORY.'/'.$id.'.json';
        if(!is_file($filePath)){
            return null;
        }
        $contact = json_decode(file_get_contents($filePath), true);
        if (!is_array($contact)){
            return null;
        }
        return $contact;
    }

    public function patchContactById(string $id, array $body): array{
        $contact = $this->getContactById($id);

        foreach ($body as $key => $value){
            $contact[$key] = $value;
        }
        $contact['dateOfLastUpdate'] = time();


        $filePath = self::CONTACT_DIRECTORY.'/'.$id.'.json';
        file_put_contents($filePath, json_encode($contact, JSON_PRETTY_PRINT));

        return $contact;
    }

    public function deleteContactById(string $id): void{
        $filePath = self::CONTACT_DIRECTORY.'/'.$id.'.json';
        unlink($filePath);
    }
}
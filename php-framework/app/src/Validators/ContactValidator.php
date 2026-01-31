<?php

namespace App\Validators;

class ContactValidator{
    private const ALLOWED_FIELDS = ['email', 'subject', 'message'];

    public function isBodyValidPost(array $body): bool{
        if($this->isBodyExtraFields($body)||$this->isBodyMissingFields()){
            return false;
        }
        return true;
    }

    public function isBodyValidPatch(array $body): bool{
        if($this->isBodyExtraFields($body)){
            return false;
        }
        return true;
    }

    private function isBodyExtraFields(array $body): bool{
        $extraFileds = array_diff(array_keys($body), self::ALLOWED_FIELDS); // return what's in array 1 but not in array2
        if (!empty($extraFileds)) {
            return false;
        }
        return true;
    }

    private function isBodyMissingFields(array $body): bool{
        $missingFileds = array_diff(self::ALLOWED_FIELDS,array_keys($body));
        if (!empty($missingFileds)) {
            return true;
        }
        return false;
    }



}
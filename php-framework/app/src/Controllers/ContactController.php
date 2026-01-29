<?php
namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;

class ContactController extends AbstractController{
    public function process(Request $request): Response{
        $body = $request->getBody();

        // check if the body is json (transferred as array)
        if (!is_array($body)) {
            return new Response('Invalid JSON body', 400, []);
        }

        // check if body has right properties and missing properties, otherwise 400
        $allowed = ['email', 'subject', 'message'];

        $extraKeys = array_diff(array_keys($body), $allowed);
        if (!empty($extraKeys)) {
            return new Response("Unexpected properties : {array_values($extraKeys) } \n Expected properties: {array_values($allowed)}", 400, []);
        }

        foreach ($allowed as $key) {
            if (!array_key_exists($key, $body)) {
                return Response("Missing property: {$key} \n", 400, []);
            }
        }

        // sava data
        $currentTimeStamp = time();
        $contact = [
            'email' => (string) $body['email'],
            'subject' => (string) $body['subject'],
            'message' => (string) $body['message'],
            'dateOfCreation' => $currentTimeStamp,
            'dateOfLastUpdate' => $currentTimeStamp,
        ];

        $contact_dir = __DIR__.'/../../var/contacts';
        $emailSafe = preg_replace('/[^A-Za-z0-9._@-]/', '_', $contact['email']);
        $file_name = $contact['dateOfCreation'].'_'.$emailSafe.'.json'; // what if mail have carac not allowed ?
        file_put_contents($file_name, json_encode($contact, JSON_PRETTY_PRINT));




        return new Response('Saved', 201, []);
    }
}
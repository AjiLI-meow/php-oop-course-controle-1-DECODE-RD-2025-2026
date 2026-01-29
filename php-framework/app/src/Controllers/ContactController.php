<?php
namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;

class ContactController extends AbstractController{
    private const ALLOWED_FIELDS = ['email', 'subject', 'message'];
    private const CONTACT_DIRECTORY = __DIR__.'/../../var/contacts';

    public function process(Request $request): Response{
        $body = $request->getBody();
















        // check if the body is json (transferred as array)
        if (!is_array($body)) {
            return new Response('Invalid JSON body', 400, []);
        }

        // check if body has right properties and missing properties, otherwise 400
        $extraKeys = array_diff(array_keys($body), self::ALLOWED_FIELDS);
        if (!empty($extraKeys)) {
            return new Response(
                'Unexpected properties: ' . implode(', ', $extraKeys) .
                "\nExpected properties: " . implode(', ', SELF::ALLOWED_FIELDS),
                400,
                []
            );
        }

        $missingKey = $this->getMissingKey($body);
        if ($missingKey !== null) {
            return new Response("Missing property: {$missingKey} \n", 400, []);
        }

        foreach (self::ALLOWED_FIELDS as $key) {
            if (!array_key_exists($key, $body)) {
                return new Response("Missing property: {$key} \n", 400, []);
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
        $fileName = $this->createFileName($contact['email'],$contact['dateOfCreation']);
        file_put_contents(SELF::CONTACT_DIRECTORY.'/'.$fileName, json_encode($contact, JSON_PRETTY_PRINT));

        $responseBody = json_encode(['file' => $fileName]);
        return new Response($responseBody, 201, ['Content-Type' => 'application/json']);
    }

    private function createFileName(string $email, int $timestamp): string{
        $emailSafe = preg_replace('/[^A-Za-z0-9._@-]/', '_', $email); //may put a wrong email address ?
        $formatted_timestamp = date('Y-m-d_H-i-s', $timestamp);
        return $formatted_timestamp . '_' . $emailSafe . '.json';
    }

    private function getMissingKey(array $body): ?string {
        foreach (self::ALLOWED_FIELDS as $key) {
            if (!array_key_exists($key, $body)) {
                return $key;
            }
        }
        return null;
    }

    private function checkBody(Request $request): bool
    {
        if ($this->body !== null) {
            return $this->body;
        }

        // Check if json
        $contentType = $this->getHeaders()["Content-Type"] ?? $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? null;
        if (stripos($contentType, 'application/json') === false) {
            throw new \RuntimeException('Content-Type must be application/json');
        }

        // get json
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (!is_array($data)) {
            throw new \RuntimeException('Invalid JSON body');
        }

        $this->body = $data;
        return $this->body;
        return false;
    }


}

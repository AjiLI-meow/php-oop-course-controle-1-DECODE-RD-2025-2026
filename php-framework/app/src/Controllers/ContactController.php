<?php
namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;
use App\Validators\ContactValidator;
use App\Services\ContactService;

class ContactController extends AbstractController{

    public function process(Request $request): Response{

        $method = $request->getMethod();

        switch ($method){
            case 'POST':
                return $this->processPost($request);
                break;
            case 'GET':
                return $this->processGet($request);
                break;
            default:
                Return new Response('Method Not Allowed', 405, []);
        }

    }

    private function isBodyJSON(Request $request): bool{
        $contentType = $request->getHeaders()["Content-Type"] ?? $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? null;
        if (stripos($contentType, 'application/json') === false) {
            return false;
        }else
            return true;
    }

    private function processPost(Request $request): Response{
        if (!$this->isBodyJSON($request)){
            return new Response("JSON required \n", 400, []);
        }

        $body = json_decode($request->getBody(), true); //return arrays

        $contactValidator = new ContactValidator();
        if(!$contactValidator->isBodyValid($body)){
            return new Response("Invalid JSON body \n", 400, []);
        }

        $contactService = new ContactService();
        $contact = $contactService->buildContact($body);
        $contactService->saveContact($contact);

        return new Response(json_encode(['file' => $contact->getId()]), 201, ['Content-type' => 'application/json']);
    }

    private function processGet(Request $request): Response{

        $contactService = new ContactService();
        $allContacts = $contactService->getAllContacts();
        $allContacts = json_encode($allContacts);

        return new Response($allContacts, 200, ['Content-type' => 'application/json']);
    }

}

<?php
namespace App\Controllers;

use App\Entities\Contact;
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
            case 'PATCH':
                return $this->processPatch($request);
                break;
            case 'DELETE':
                return $this->processDelete($request);
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
        $body = $this->getJsonBody($request);
        $validation = $this->isBodyValid($body,$request->getMethod());
        if(!is_bool($validation)){
            return $validation;
        }

        $contactService = new ContactService();
        $contact = $contactService->buildContact($body);
        $contactService->saveContact($contact);


        return new Response(json_encode(['file' => $contact->getId().'.json']), 201, ['Content-type' => 'application/json']);
    }

    private function processGetAll(Request $request): Response{
        $contactService = new ContactService();
        $allContacts = $contactService->getAllContacts();
        $allContacts = json_encode($allContacts);

        return new Response($allContacts, 200, ['Content-type' => 'application/json']);
    }

    private function processGetById(Request $request): Response{
        $id = $request->getParams()['id'];

        $contact = $this->checkContactById($id);
        if($contact instanceof Response){
            return $contact;
        }
        $contact = json_encode($contact);

        return new Response($contact,200, ['Content-type' => 'application/json']);
    }

    private function processGet(Request $request): Response{
        if ($request->getParams()){
            return $this->processGetById($request);
        }
        return $this->processGetAll($request);
    }

    private function processPatch(Request $request): Response{
        $id = $request->getParams()['id'];
        $isContactExisting = $this->checkContactById($id);
        if($isContactExisting instanceof Response){
            return $isContactExisting;
        }

        $body = $this->getJsonBody($request);
        $validation = $this->isBodyValid($body,$request->getMethod());
        if(!is_bool($validation)){
            return $validation;
        }

        $contactService = new ContactService();
        $contact = $contactService->patchContactById($id, $body);
        $contact = json_encode($contact);

        return new Response($contact,200, ['Content-type' => 'application/json']);
    }

    private function getJsonBody(Request $request): array|Response{
        if (!$this->isBodyJSON($request)){
            return new Response("JSON required \n", 400, []);
        }
        return json_decode($request->getBody(), true); //return arrays
    }

    private function isBodyValid(array $body,string $method):bool|Response{
        $contactValidator = new ContactValidator();
        $res = true;
        switch ($method){
            case 'POST':
                $res = $contactValidator->isBodyValidPost($body);
                break;
            case 'PATCH':
                $res = $contactValidator->isBodyValidPatch($body);
                break;
        }
        if ($res === false){
            return new Response("Invalid JSON body \n", 400, []);
        }else{
            return $res;
        }

    }

    private function checkContactById(string $id):array|Response{
        $contactService = new ContactService();
        $contact = $contactService->getContactById($id);
        if ($contact === null){
            return new Response("Contact not found \n", 404, []);
        }
        return $contact;
    }

    private function processDelete(Request $request): Response{
        $id = $request->getParams()['id'];
        $isContactExisting = $this->checkContactById($id);
        if($isContactExisting instanceof Response){
            return $isContactExisting;
        }

        $contactService = new ContactService();
        $contactService->deleteContactById($id);

        return new Response("", 204, []);
    }
}

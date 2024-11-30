<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Entities\Contact;
use App\Controllers\AbstractController;
use Directory;

class PatchContactController extends AbstractController
{
  public function process(Request $request,): Response
  {
    $uri = $request->getUri();
    $email = basename($uri);

    return $this->updateContact($request, $email);
  }

  private function updateContact(Request $request, string $email): Response
  {
    $directory = __DIR__ . "/../../var/contacts/";

    // Add .json extension if missing
    if (!str_ends_with($email, '.json')) {
      $email .= '.json';
    }

    $filePath = $directory . $email;

    if (!file_exists($filePath)) {
      error_log("File not found: " . $filePath);
      return new Response(json_encode(["error" => "Contact not found"]), 404, ['Content-Type' => 'application/json']);
    }

    $body = json_decode($request->getBody(), true);

    if (!Contact::fieldsCheckValid($body)) {
      error_log("Invalid keys in request body.");
      return new Response(json_encode(["error" => "Invalid keys in request body"]), 400, ['Content-Type' => 'application/json']);
    }

    $contact = Contact::jsonRequestReader($filePath);
    $contact->update($body);
    $contact->saveFile($filePath);

    return $this->jsonResponse($contact->bodyArray(), 200);
  }
}

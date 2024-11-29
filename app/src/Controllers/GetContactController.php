<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Entities\Contact;
use App\Controllers\AbstractController;

class GetContactController extends AbstractController
{
  public function process(Request $request): Response
  {
    $uri = $request->getUri();
    $email = basename($uri);
    $contact = Contact::findOne($email);

    if ($contact === null) {
      return new Response('Contact not found', 404);
    }

    return $this->jsonResponse($contact, 200);
  }
}

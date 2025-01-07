<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
  #[Route("/api/auth")]
  #[IsGranted("ROLE_USER")]
  public function auth()
  {
    $user = $this->getUser();
    return $this->json($user, 200, [], [
      'groups' => ['api.auth']
    ]);
  }
}

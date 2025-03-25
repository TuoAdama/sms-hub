<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class HomeController extends AbstractController
{
    #[Route('/', name: "home")]
    public function index(#[CurrentUser] User $user): Response
    {
        if (in_array("ROLE_ADMIN", $user->getRoles())){
            return $this->redirectToRoute('app_admin_sms_index');
        }
        return $this->redirectToRoute('app_documentation');
    }
}

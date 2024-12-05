<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class NumberController extends AbstractController
{
    #[Route('/number/register', name: "app_number_register")]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function index(): Response
    {
        return $this->render('pages/number/number_register.html.twig');
    }
}

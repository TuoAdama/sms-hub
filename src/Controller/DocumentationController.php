<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DocumentationController extends AbstractController
{
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    #[Route('/documentation', name: 'app_documentation')]
    public function index(): Response
    {
        return $this->render('documentation/index.html.twig');
    }
}

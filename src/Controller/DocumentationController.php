<?php

namespace App\Controller;

use Parsedown;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DocumentationController extends AbstractController
{
    public function __construct(
        #[Autowire(param: "project_dir")]
        private readonly string $projectDir,
    )
    {
    }

    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    #[Route('/documentation', name: 'app_documentation')]
    public function index(): Response
    {
        $parseDown = new Parsedown();
        $readme = $parseDown->text(file_get_contents($this->projectDir."/README.md"));
        return $this->render('documentation/index.html.twig', [
            'readme' => $readme,
        ]);
    }
}

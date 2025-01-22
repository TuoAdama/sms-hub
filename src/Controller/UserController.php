<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\Token\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/user', name: 'app_user_')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly TokenService $tokenService,
        private readonly EntityManagerInterface $entityManager,
        private readonly TranslatorInterface $translator
    )
    {
    }

    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    #[Route('/token/generate', name: 'generate_token', methods: ['GET'])]
    public function generateToken(#[CurrentUser] User $user): Response
    {
        $type = "danger"; $message = $this->translator->trans('error.message');
        if ($user->isVerified() && $user->isNumberVerified()){
            $type = "success"; $message = $this->translator->trans('success.message');
            $user->setAccessToken($this->tokenService->generate($user));
            $this->entityManager->flush();
        }
        $this->addFlash($type, $message);
        return $this->redirectToRoute('home');
    }
}

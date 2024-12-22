<?php

namespace App\Controller;

use App\Form\SendResetPasswordFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


class SecurityController extends AbstractController
{

    public function __construct(
        private readonly TokenGeneratorInterface $tokenGenerator,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
    )
    {
    }

    #[Route('/reset-password', name: 'app_reset_password')]
    public function resetPassword(Request $request, TokenGeneratorInterface $tokenGenerator): Response
    {
        $form = $this->createForm(SendResetPasswordFormType::class);
        $isSubmit = false;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $isSubmit = true;
            $user = $this->userRepository->findOneBy(['email' => $form->get('email')->getData()]);
            if ($user) {
                $token = $tokenGenerator->generateToken();
                $user->setResetPasswordToken($token);
                $this->entityManager->flush();
            }
        }
        return $this->render('login/password/reset_password.html.twig', [
            'form' => $form,
            'isSubmit' => $isSubmit,
        ]);
    }

    #[Route('/reset-password', name: 'app_reset_password_form')]
    public function resetPasswordForm(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/SecurityController.php',
        ]);
    }
}

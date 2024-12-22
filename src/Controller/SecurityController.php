<?php

namespace App\Controller;

use App\Form\SendResetPasswordFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


class SecurityController extends AbstractController
{

    public function __construct(
        private readonly TokenGeneratorInterface $tokenGenerator,
        private readonly EntityManagerInterface  $entityManager,
        private readonly UserRepository          $userRepository,
        private readonly MailerInterface         $mailer,
        private readonly string                  $supportEmail, private readonly TranslatorInterface $translator,
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/reset-password', name: 'app_reset_password')]
    public function resetPassword(Request $request): Response
    {
        $form = $this->createForm(SendResetPasswordFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userRepository->findOneBy(['email' => $form->get('email')->getData()]);
            if ($user) {
                $token = $this->tokenGenerator->generateToken();
                $user->setResetPasswordToken($token);
                $this->entityManager->flush();

                $mail = new TemplatedEmail();

                $mail
                    ->from($this->supportEmail)
                    ->to($user->getEmail())
                    ->subject($this->translator->trans('password_forget.email.subject'))
                    ->htmlTemplate('login/password/reset_password_email.html.twig')
                    ->context([
                        'resetPasswordLink' => $this->generateUrl('app_reset_password_form', ['token' => $token], true)
                    ]);

                $this->mailer->send($mail);
            }
            $this->addFlash('success', $this->translator->trans('password_forget.confirm'));
        }

        return $this->render('login/password/reset_password.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password_form')]
    public function resetPasswordForm(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/SecurityController.php',
        ]);
    }
}

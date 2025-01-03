<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordFormType;
use App\Form\SendResetPasswordFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
        private readonly string                  $supportEmail, private readonly TranslatorInterface $translator, private readonly UserPasswordHasherInterface $userPasswordHasher,
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
        $emptyForm = clone $form;
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
                        'resetPasswordLink' => $this->generateUrl('app_reset_password_form', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL)
                    ]);

                $this->mailer->send($mail);
            }
            $this->addFlash('success', $this->translator->trans('password_forget.confirm'));
            $form = $emptyForm;
        }

        return $this->render('login/password/reset_password.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password_form')]
    public function resetPasswordForm(
        #[MapEntity(
            mapping: ['token' => 'resetPasswordToken']
        )] User $user,
        Request $request
    ): Response
    {
        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            $user->setPassword(
                $this->userPasswordHasher->hashPassword($user, $password)
            );
            $user->setResetPasswordToken(null);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_login');
        }
        return $this->render('login/password/reset_password_form.html.twig', [
            'form' => $form
        ]);
    }
}

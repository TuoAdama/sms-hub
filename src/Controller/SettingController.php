<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

class SettingController extends AbstractController
{


    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TranslatorInterface $translator,
        private readonly EmailVerifier $emailVerifier
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/setting', name: 'app_setting')]
    #[isGranted('IS_AUTHENTICATED_FULLY')]
    public function index(#[CurrentUser] User $user, Request $request): Response
    {
        $form = $this->createForm(UserFormType::class, $user);
        $session = $request->getSession();
        $session->set("email", $user->getEmail());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $oldEmail = $session->get('email');
            if ($user->getEmail() !== $oldEmail) {
                $user->setVerified(false);
                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user);
                $this->addFlash('warning', $this->translator->trans("email.edit"));
            }
            $this->addFlash('success', $this->translator->trans("success.message"));
            $this->entityManager->flush();
        }
        return $this->render('pages/setting/setting.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }
}

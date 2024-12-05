<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\NumberVerification;
use App\Entity\SmsMessage;
use App\Entity\User;
use App\Form\NumberFormType;
use App\Repository\SmsMessageRepository;
use App\Repository\UserRepository;
use App\Service\SmsMessageService;
use App\Service\Token\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use function Symfony\Component\Clock\now;

class NumberController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TokenService $tokenService,
        private readonly UserRepository $userRepository,
        private readonly string $adminEmail,
        private readonly SmsMessageService $smsMessageService,
        private readonly TranslatorInterface $translator,
    )
    {
    }

    /**
     * @throws RandomException
     */
    #[Route('/number/register', name: "app_number_register")]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function index(#[CurrentUser] User $user, Request $request): Response
    {
        $form = $this->createForm(NumberFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $countryCode = $form->get('countryCode')->getData();
            $number = $form->get('number')->getData();
            $user->setNumber("+".$countryCode.$number);
            $this->handleNumberVerification($user);
            return $this->redirectToRoute('home');
        }
        return $this->render('pages/number/number_register.html.twig', [
            'form' => $form,
        ]);
    }


    /**
     * @throws RandomException
     * @throws \Exception
     */
    private function handleNumberVerification(User $user): void {
        $numberVerification = $user->getNumberVerification();
        if ($numberVerification !== null) {
            $this->entityManager->remove($numberVerification);
        }
        $verification = new NumberVerification();

        $verificationCode = random_int(1000, 9999);

        $verification->setUser($user)
            ->setVerified(false)
            ->setToken($this->tokenService->generateNumberVerificationToken($user))
            ->setTemporalCode($verificationCode);

        $smsMessage = new SmsMessage();
        $smsMessage->setUser($this->userRepository->findAdminByEmail($this->adminEmail) ?: throw new Exception("Admin not found"))
            ->setMessage($this->translator->trans('verification.number.message'). $verificationCode)
            ->setRecipient($user->getNumber())
            ->setCreatedAt(now());

        $this->smsMessageService->store($smsMessage);

        $this->entityManager->persist($verification);
        $this->entityManager->flush();
    }
}

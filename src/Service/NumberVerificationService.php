<?php

namespace App\Service;

use App\Entity\NumberVerification;
use App\Entity\SmsMessage;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Token\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use Symfony\Contracts\Translation\TranslatorInterface;
use function Symfony\Component\Clock\now;

readonly class NumberVerificationService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private TokenService                    $tokenService,
        private UserRepository         $userRepository,
        private string                 $adminEmail,
        private SmsMessageService      $smsMessageService,
        private TranslatorInterface    $translator,
    )
    {
    }


    /**
     * @throws RandomException
     * @throws \Exception
     */
    public function handleNumberVerification(User $user): void {
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

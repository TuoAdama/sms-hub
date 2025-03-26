<?php

namespace App\Service;

use App\Entity\SmsMessage;
use App\Entity\User;
use App\Service\Token\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Random\RandomException;
use Symfony\Contracts\Translation\TranslatorInterface;
use function Symfony\Component\Clock\now;

readonly class NumberService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private TokenService                    $tokenService,
        private string                 $adminEmail,
        private TranslatorInterface    $translator,
        private PhoneNumberUtil        $phoneNumberUtil,
    )
    {
    }


    /**
     * @throws RandomException
     * @throws \Exception
     */
    public function handleNumberVerification(User $user): void {

        $verificationCode = random_int(1000, 9999);

        $user
            ->setNumberVerified(false)
            ->setNumberToken($this->tokenService->generateNumberVerificationToken($user))
            ->setNumberTemporalCode($verificationCode);

        $smsMessage = new SmsMessage();
        $smsMessage
            ->setMessage($this->translator->trans('verification.number.message'). $verificationCode)
            ->setRecipient($user->getNumber())
            ->setCreatedAt(now());

        $this->entityManager->persist($smsMessage);

        $this->entityManager->flush();
    }


    public function formatNumber(PhoneNumber $number): string
    {
        return $this->phoneNumberUtil->format(
            $number,
            PhoneNumberFormat::E164
        );
    }
}

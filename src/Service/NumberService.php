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
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Translation\TranslatorInterface;
use function Symfony\Component\Clock\now;

readonly class NumberService
{

    private array $countriesCodesAccepted;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private TokenService                    $tokenService,
        private string                 $adminEmail,
        private TranslatorInterface    $translator,
        private PhoneNumberUtil        $phoneNumberUtil,
        #[Autowire(param: "countries_codes")]
        private readonly array $countriesCodes
    )
    {
        $this->countriesCodesAccepted = $this->countriesCodes['accepted'] ?? [];
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

    public function isNumberAccepted(PhoneNumber $phoneNumber): bool
    {
        return in_array($phoneNumber->getCountryCode(), $this->countriesCodesAccepted);
    }
}

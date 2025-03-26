<?php

namespace App\Service;

use App\DTO\Request\SmsMessageDTO;
use App\Entity\SmsMessage;
use App\Entity\User;
use App\Repository\SmsMessageRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class SmsMessageService
{

    public function __construct(
        private SmsMessageRepository   $smsMessageRepository,
        private EntityManagerInterface $entityManager,
        #[Autowire(param: 'trial_mode')]
        private bool                   $trialMode,
        private NumberService          $numberService,
    )
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    function getAllUnsentSmsMessages(): array
    {
        return $this->smsMessageRepository->getAllUnsentSmsMessages();
    }


    public function getUnSentMessageByUser(User $user): array
    {
        return $this->smsMessageRepository->findBy([
            'user' => $user,
            'sent' => false,
        ]);
    }

    /**
     * @throws NumberParseException
     */
    public function storeFromRequest(SmsMessageDTO $smsMessageDTO, UserInterface $user): SmsMessage
    {
        $phoneNumber = $this->numberService->formatNumber(
            PhoneNumberUtil::getInstance()->parse($smsMessageDTO->to)
        );

        $smsMessage = new SmsMessage();

        /** @var User $user */
        $smsMessage->setUser($user)
            ->setMessage($smsMessageDTO->message)
            ->setRecipient($phoneNumber)
            ->setCreatedAt(new DateTimeImmutable());

        if ($this->trialMode) {
            $smsMessage->setRecipient($user->getNumber());
        }

        $this->entityManager->persist($smsMessage);
        $this->entityManager->flush();
        return $smsMessage;
    }

    public function store(SmsMessage $smsMessage): void
    {
        $this->entityManager->persist($smsMessage);
        $this->entityManager->flush();
    }
}

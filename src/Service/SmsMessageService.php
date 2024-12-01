<?php

namespace App\Service;

use App\DTO\SmsMessageDTO;
use App\Entity\SmsMessage;
use App\Entity\User;
use App\Repository\SmsMessageRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\User\UserInterface;

class SmsMessageService
{

    public function __construct(
        private readonly SmsMessageRepository $smsMessageRepository,
        private readonly EntityManagerInterface $entityManager
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

    public function storeFromRequest(SmsMessageDTO $smsMessageDTO, UserInterface $user): SmsMessage
    {
        $smsMessage = new SmsMessage();

        /** @var User $user */
        $smsMessage->setUser($user)
            ->setMessage($smsMessageDTO->message)
            ->setRecipient($smsMessageDTO->to)
            ->setCreatedAt(new DateTimeImmutable());

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
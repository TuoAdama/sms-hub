<?php

namespace App\Service;

use App\Entity\SmsMessage;
use App\Repository\SmsMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

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

    public function store(SmsMessage $smsMessage): void
    {
        $this->entityManager->persist($smsMessage);
        $this->entityManager->flush();
    }
}
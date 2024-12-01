<?php

namespace App\Controller\API;

use App\Service\SmsMessageService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/messages')]
class SmsMessageAPI extends AbstractController
{
    public function __construct(
        private readonly SmsMessageService $smsMessageService
    )
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/unsent', methods: ['GET'])]
    public function getUnsentMessages(): JsonResponse
    {
        return $this->json($this->smsMessageService->getAllUnsentSmsMessages());
    }
}
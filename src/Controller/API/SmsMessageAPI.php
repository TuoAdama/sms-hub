<?php

namespace App\Controller\API;

use App\DTO\Request\SmsMessageDTO;
use App\Service\SmsMessageService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
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

    #[Route('/store', methods: ['POST'])]
    public function store(#[MapRequestPayload] SmsMessageDTO $message): JsonResponse
    {
        $smsMessage = $this->smsMessageService->storeFromRequest($message, $this->getUser());
        return $this->json($smsMessage, Response::HTTP_CREATED);
    }
}
<?php

namespace App\Controller\API;

use App\DTO\Request\SmsMessageDTO;
use App\Entity\User;
use App\Service\SmsMessageService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SmsMessageControllerAPI extends AbstractController
{
    public function __construct(
        private readonly SmsMessageService $smsMessageService
    )
    {
    }


    /**
     * @throws NonUniqueResultException
     */
    #[Route('/api/messages', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function getAllUnsentMessages(): JsonResponse
    {
        return $this->json($this->smsMessageService->getAllUnsentSmsMessages());
    }

    #[Route('/api/messages', methods: ['POST'])]
    public function store(#[MapRequestPayload] SmsMessageDTO $message): Response
    {
        $smsMessage = $this->smsMessageService->storeFromRequest($message, $this->getUser());
        return $this->json($smsMessage, Response::HTTP_CREATED);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/api/messages/unsent', methods: ['GET'])]
    public function getUnsentMessagesByUser(#[CurrentUser] User $user): JsonResponse
    {
        return $this->json($this->smsMessageService->getUnSentMessageByUser($user));
    }
}

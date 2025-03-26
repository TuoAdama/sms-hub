<?php

namespace App\Controller\API;

use App\DTO\Request\SmsMessageDTO;
use App\Entity\User;
use App\Service\NumberService;
use App\Service\SmsMessageService;
use Doctrine\ORM\NonUniqueResultException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

class SmsMessageControllerAPI extends AbstractController
{
    public function __construct(
        private readonly SmsMessageService $smsMessageService,
        private readonly NumberService     $numberService, private readonly TranslatorInterface $translator,
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

    /**
     * @throws NumberParseException
     */
    #[Route('/api/messages', methods: ['POST'])]
    public function store(#[MapRequestPayload] SmsMessageDTO $message): Response
    {
        $phoneNumber = PhoneNumberUtil::getInstance()->parse($message->to);

        $isAcceptedNumber = $this->numberService->isNumberAccepted($phoneNumber);

        if (!$isAcceptedNumber) {
            return $this->json([
                "message" => $this->translator->trans("country_code_not_accepted"),
            ], Response::HTTP_BAD_REQUEST);
        }

        $message->to = $this->numberService->formatNumber(
            $phoneNumber,
        );
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

<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\DTO\Request\UserAuthDTO;
use App\Repository\UserRepository;
use App\Service\Token\TokenGenerator;
use App\Service\Token\TokenService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

class TokenControllerAPI extends AbstractController
{
    public function __construct(
        private readonly UserRepository              $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface      $entityManager,
        private readonly TokenService                $tokenService,
        private readonly TranslatorInterface $translator,
    )
    {
    }
    #[Route('/token/generate', name: 'generate_token', methods: ['POST'])]
    public function generate(#[MapRequestPayload] UserAuthDTO $userAuth): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['email' => $userAuth->username]);
        if (
            $user === null
            || !$this->passwordHasher->isPasswordValid($user, $userAuth->password)
        ) {
            return new JsonResponse(['error' => $this->translator->trans('user.credential.invalid')], Response::HTTP_BAD_REQUEST);
        }

        if (!$user->isNumberVerified() || !$user->isVerified()){
            return new JsonResponse([
                'error' => $this->translator->trans('user.credential.unverified')
            ], Response::HTTP_BAD_REQUEST);
        }

        $token = $this->tokenService->generate($user);
        $user->setAccessToken($token);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'token' => $token,
        ]);
    }
}

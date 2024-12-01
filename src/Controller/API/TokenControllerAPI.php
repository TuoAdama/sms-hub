<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\DTO\Request\UserAuthDTO;
use App\Repository\UserRepository;
use App\Service\Token\TokenGenerator;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
class TokenControllerAPI extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly TokenGenerator $tokenGenerator
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
            return new JsonResponse(['error' => 'username or password is incorrect'], Response::HTTP_BAD_REQUEST);
        }
        $iat = strtotime('+2 months');
        $token = $this->tokenGenerator->setPayload(
            $userAuth->username,
            $userAuth->username,
            $iat,
        )->generate();

        return new JsonResponse([
            'token' => $token,
            'expires_at' => date('Y-m-d H:i:s', $iat),
        ]);
    }
}

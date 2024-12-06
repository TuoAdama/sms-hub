<?php

namespace App\Service\Token;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class TokenService
{
    public function __construct(
        private TokenGenerator $tokenGenerator,
    )
    {
    }

    public function generate(User $user): string
    {
        $iat = strtotime('+2 months');
        return $this->tokenGenerator->setPayload(
            strval($user->getId()),
            $user->getName(),
            $iat,
        )->generate();
    }

    public function generateNumberVerificationToken(User $user): string
    {
        return $this->tokenGenerator->setPayload(
            strval($user->getId()),
            $user->getName(),
            strtotime('+4 minutes'),
            [
                'type' => 'number verification',
            ]
        )->generate();
    }
}

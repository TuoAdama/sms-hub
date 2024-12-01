<?php

namespace App\Service\Token;

use App\Entity\User;

interface TokenGenerator
{
    public function setHeader(array $headers): TokenGenerator;

    public function setPayload(string $sub, string $name, int $iat, array $more = []): TokenGenerator;

    public function generate(): string;

    public function decode(string $token): array;
}
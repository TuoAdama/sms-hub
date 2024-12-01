<?php

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class UserAuthDTO
{
    #[NotNull]
    #[NotBlank]
    public string $username;

    #[NotNull]
    #[NotBlank]
    public string $password;
}
<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SmsMessageDTO
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Regex(pattern: "/^0[1-9]{1}[0-9]{8}$/")]
    public string $to;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public string $message;
}
<?php

namespace App\DTO\Request;

use App\Validator\OnlyAcceptedCountries;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;

class SmsMessageDTO
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[PhoneNumber]
    #[OnlyAcceptedCountries]
    public string $to;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public string $message;
}

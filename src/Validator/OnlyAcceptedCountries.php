<?php

namespace App\Validator;


use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class OnlyAcceptedCountries extends Constraint
{
    public string $message = 'country_code_not_accepted';

    public function __construct(
        ?array $groups = null,
        mixed $payload = null,
    )
    {
        parent::__construct([], $groups, $payload);
    }
}

<?php

namespace App\Validator;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueNumberValidator extends ConstraintValidator
{

    public function __construct(
        private readonly UserRepository $userRepository,
    )
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueNumber) {
            throw new UnexpectedTypeException($constraint, UniqueNumber::class);
        }
        if ($value === null || $value === '') {
            return;
        }
        if (!is_string($value)) {
            return;
        }
        $user = $this->userRepository->findOneBy(['number' => $value, 'isNumberVerified' => true]);
        if ($user !== null) {
            $this->context->buildViolation($constraint->message)
                ->atPath('number')
                ->addViolation();
        }
    }
}

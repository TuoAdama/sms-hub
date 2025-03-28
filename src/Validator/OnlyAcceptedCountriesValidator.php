<?php

namespace App\Validator;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class OnlyAcceptedCountriesValidator extends ConstraintValidator
{

    /**
     * @var array|mixed
     */
    private array $countriesCodesAccepted;

    #[HasNamedArguments]
    public function __construct(
        #[Autowire(param: "countries_codes")]
        private readonly array $countriesCodes,
        private readonly TranslatorInterface $translator
    )
    {
        $this->countriesCodesAccepted = $this->countriesCodes['accepted'] ?? [];
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof OnlyAcceptedCountries) {
            throw new UnexpectedTypeException($constraint, OnlyAcceptedCountries::class);
        }

        if (null === $value || '' === $value) {
            return;
        }



        if (
            gettype($value) !== 'string' && !($value instanceof PhoneNumber)
        ) {
            throw new UnexpectedTypeException($value, 'string or PhoneNumber object');
        }


        $phoneNumber = $value;

        if (gettype($value) === 'string'){

            if (!PhoneNumberUtil::isViablePhoneNumber($value)) {
                return;
            }

            try {
                $phoneNumber = PhoneNumberUtil::getInstance()->parse(
                    $value,
                );
            }catch (NumberParseException $e) {
                $this->context->buildViolation(
                    $this->translator->trans("invalid.country_code")
                )
                    ->addViolation();

                return;
            }
        }


        if (!in_array($phoneNumber->getCountryCode(), $this->countriesCodesAccepted)){
            $this->context->buildViolation(
                $this->translator->trans($constraint->message)
            )
                ->addViolation();
        }
    }
}

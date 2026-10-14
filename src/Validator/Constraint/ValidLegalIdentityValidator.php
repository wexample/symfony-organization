<?php

namespace Wexample\SymfonyOrganization\Validator\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Wexample\SymfonyOrganization\Interface\LegalIdentityInterface;
use Wexample\SymfonyOrganization\Service\Identifier\LegalIdentifierSchemeRegistry;

class ValidLegalIdentityValidator extends ConstraintValidator
{
    public function __construct(
        private readonly LegalIdentifierSchemeRegistry $schemeRegistry,
    ) {
    }

    public function validate(
        mixed $value,
        Constraint $constraint
    ): void {
        if (! $constraint instanceof ValidLegalIdentity) {
            throw new UnexpectedTypeException($constraint, ValidLegalIdentity::class);
        }

        if (! $value instanceof LegalIdentityInterface) {
            return;
        }

        $scheme = $this->schemeRegistry->forIdentity($value);

        if ($value->getLegalIdentifier() && ! $scheme->isLegalIdentifierValid($value->getLegalIdentifier())) {
            $this->context->buildViolation($constraint->legalIdentifierMessage)
                ->setTranslationDomain('organization')
                ->atPath('legalIdentifier')
                ->addViolation();
        }

        if ($value->hasVatNumber() && ! $scheme->isVatNumberValid((string) $value->getVatNumber())) {
            $this->context->buildViolation($constraint->vatNumberMessage)
                ->setTranslationDomain('organization')
                ->atPath('vatNumber')
                ->addViolation();
        }
    }
}

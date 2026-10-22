<?php

namespace Wexample\SymfonyOrganization\Validator\Constraint;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * On a LegalIdentityInterface: its legal identifier and its VAT number follow
 * the rules of its country (LegalIdentifierSchemeInterface). Empty values pass.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class ValidLegalIdentity extends Constraint
{
    public string $legalIdentifierMessage = 'identity.error.legal_identifier';

    public string $vatNumberMessage = 'identity.error.vat_number';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}

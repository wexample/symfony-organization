<?php

namespace Wexample\SymfonyOrganization\Service\Identifier;

use Wexample\SymfonyOrganization\Helper\BeIdentityHelper;

/**
 * Belgium: the enterprise number (numéro d'entreprise / ondernemingsnummer),
 * the VAT number being BE followed by the same ten digits.
 */
class BeLegalIdentifierScheme extends AbstractLegalIdentifierScheme
{
    public function getCountryCode(): ?string
    {
        return 'BE';
    }

    public function getLegalIdentifierLabel(): string
    {
        return 'identity.be.enterprise_number';
    }

    public function getVatNumberLabel(): string
    {
        return 'identity.be.vat_number';
    }

    public function normalizeLegalIdentifier(string $identifier): string
    {
        return BeIdentityHelper::normalize($identifier);
    }

    public function formatLegalIdentifier(string $identifier): string
    {
        return BeIdentityHelper::format($identifier);
    }

    public function isLegalIdentifierValid(string $identifier): bool
    {
        return BeIdentityHelper::isValidEnterpriseNumber($identifier);
    }

    public function isVatNumberValid(string $vatNumber): bool
    {
        return $this->isOwnVatNumber($vatNumber)
            ? BeIdentityHelper::isValidVatNumber($vatNumber)
            : parent::isVatNumberValid($vatNumber);
    }

    public function isLegalIdentifierIncludedInVatNumber(): bool
    {
        return true;
    }

    public function buildVatNumber(string $identifier): ?string
    {
        return BeIdentityHelper::isValidEnterpriseNumber($identifier) ? BeIdentityHelper::toVatNumber($identifier) : null;
    }
}

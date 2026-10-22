<?php

namespace Wexample\SymfonyOrganization\Service\Identifier;

use Wexample\SymfonyOrganization\Helper\FrIdentityHelper;

/**
 * France: SIREN (9 digits) or SIRET (SIREN + 5-digit establishment), the
 * intracommunity VAT number being FR + a 2-digit key + the SIREN.
 */
class FrLegalIdentifierScheme extends AbstractLegalIdentifierScheme
{
    public function getCountryCode(): ?string
    {
        return 'FR';
    }

    public function getLegalIdentifierLabel(): string
    {
        return 'identity.fr.siret';
    }

    public function getVatNumberLabel(): string
    {
        return 'identity.fr.vat_number';
    }

    public function normalizeLegalIdentifier(string $identifier): string
    {
        return FrIdentityHelper::normalize($identifier);
    }

    public function formatLegalIdentifier(string $identifier): string
    {
        $identifier = $this->normalizeLegalIdentifier($identifier);

        // "732 829 320" for a SIREN, "732 829 320 00009" for a SIRET.
        return trim(substr($identifier, 0, 3).' '.substr($identifier, 3, 3).' '.substr($identifier, 6, 3).' '.substr($identifier, 9));
    }

    public function isLegalIdentifierValid(string $identifier): bool
    {
        return FrIdentityHelper::isValidSiret($identifier) || FrIdentityHelper::isValidSiren($identifier);
    }

    public function isVatNumberValid(string $vatNumber): bool
    {
        return $this->isOwnVatNumber($vatNumber)
            ? FrIdentityHelper::isValidVatNumber($vatNumber)
            : parent::isVatNumberValid($vatNumber);
    }

    public function buildVatNumber(string $identifier): ?string
    {
        $siren = FrIdentityHelper::sirenOf($identifier);

        return $siren ? FrIdentityHelper::buildVatNumber($siren) : null;
    }
}

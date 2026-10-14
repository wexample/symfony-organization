<?php

namespace Wexample\SymfonyOrganization\Service\Identifier;

use Wexample\SymfonyOrganization\Interface\LegalIdentifierSchemeInterface;

/**
 * The generic rules, which a country's scheme refines.
 */
abstract class AbstractLegalIdentifierScheme implements LegalIdentifierSchemeInterface
{
    public function getLegalIdentifierLabel(): string
    {
        return 'identity.legal_identifier';
    }

    public function getVatNumberLabel(): string
    {
        return 'identity.vat_number';
    }

    public function normalizeLegalIdentifier(string $identifier): string
    {
        return strtoupper(preg_replace('/[\s.\-]/', '', $identifier));
    }

    public function formatLegalIdentifier(string $identifier): string
    {
        return $this->normalizeLegalIdentifier($identifier);
    }

    public function isLegalIdentifierValid(string $identifier): bool
    {
        return '' !== trim($identifier);
    }

    public function isVatNumberValid(string $vatNumber): bool
    {
        return 1 === preg_match('/^[A-Z]{2}[0-9A-Z]{2,13}$/', strtoupper(preg_replace('/[\s.\-]/', '', $vatNumber)));
    }

    public function isLegalIdentifierIncludedInVatNumber(): bool
    {
        return false;
    }

    public function buildVatNumber(string $identifier): ?string
    {
        return null;
    }

    /**
     * The country's own VAT numbers follow its rules, the others the generic ones.
     */
    protected function isOwnVatNumber(string $vatNumber): bool
    {
        return str_starts_with(strtoupper(trim($vatNumber)), (string) $this->getCountryCode());
    }
}

<?php

namespace Wexample\SymfonyOrganization\Interface;

/**
 * How one country identifies its organizations: the name and the rules of
 * its legal identifier (SIREN/SIRET, BCE enterprise number…) and of its VAT
 * numbers. One small class per country, registered by implementing this
 * interface; a country without one gets the generic rules.
 */
interface LegalIdentifierSchemeInterface
{
    /** ISO 3166-1 alpha-2; null for the generic scheme. */
    public function getCountryCode(): ?string;

    /** Translation key (domain `organization`) of the legal identifier's name. */
    public function getLegalIdentifierLabel(): string;

    /** Translation key (domain `organization`) of the VAT number's name. */
    public function getVatNumberLabel(): string;

    /** As stored: what search and deduplication compare. */
    public function normalizeLegalIdentifier(string $identifier): string;

    /** As printed. */
    public function formatLegalIdentifier(string $identifier): string;

    public function isLegalIdentifierValid(string $identifier): bool;

    public function isVatNumberValid(string $vatNumber): bool;

    /**
     * Whether the VAT number contains the legal identifier (BE), so that a
     * document printing the VAT number need not print the identifier too.
     */
    public function isLegalIdentifierIncludedInVatNumber(): bool;

    /** The VAT number this legal identifier implies, when the country derives one. */
    public function buildVatNumber(string $identifier): ?string;
}

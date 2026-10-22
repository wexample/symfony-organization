<?php

namespace Wexample\SymfonyOrganization\Interface;

use Wexample\SymfonyGeo\Entity\Address;
use Wexample\SymfonyGeo\Entity\Country;

/**
 * Who someone is, legally: what an invoice prints about its issuer and its
 * client, what VIES checks, what a legal identifier scheme validates.
 */
interface LegalIdentityInterface
{
    public function getName(): string;

    /** SIREN/SIRET, BCE enterprise number…, as the country names it. */
    public function getLegalIdentifier(): ?string;

    /** With its country prefix, normalized (no space, dot nor dash). */
    public function getVatNumber(): ?string;

    public function hasVatNumber(): bool;

    /** "SAS", "SRL", "EI", "ASBL"… as the country knows them. */
    public function getLegalForm(): ?string;

    /** A private person rather than a business (B2C rules, no VAT number). */
    public function isIndividual(): bool;

    public function getEmail(): ?string;

    public function getPhone(): ?string;

    public function getWebsite(): ?string;

    /** The country it is registered in: its identifier scheme, its taxes. */
    public function getCountry(): ?Country;

    /** Where it is, read live — a document keeps an AddressSnapshot instead. */
    public function getAddress(): ?Address;
}

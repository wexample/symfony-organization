<?php

namespace Wexample\SymfonyOrganization\Entity\Traits;

use Wexample\SymfonyGeo\Entity\Address;
use Wexample\SymfonyGeo\Entity\Country;

/**
 * LegalIdentityInterface read from the organization an entity points to
 * (HasOrganizationInterface): a ledger or a party reads as its organization,
 * while the identity itself is changed on the organization, once, for all
 * who point to it.
 */
trait DelegatesLegalIdentityTrait
{
    public function getName(): string
    {
        return $this->getOrganization()->getName();
    }

    public function getLegalIdentifier(): ?string
    {
        return $this->getOrganization()->getLegalIdentifier();
    }

    public function getVatNumber(): ?string
    {
        return $this->getOrganization()->getVatNumber();
    }

    public function hasVatNumber(): bool
    {
        return $this->getOrganization()->hasVatNumber();
    }

    public function getLegalForm(): ?string
    {
        return $this->getOrganization()->getLegalForm();
    }

    public function isIndividual(): bool
    {
        return $this->getOrganization()->isIndividual();
    }

    public function getEmail(): ?string
    {
        return $this->getOrganization()->getEmail();
    }

    public function getPhone(): ?string
    {
        return $this->getOrganization()->getPhone();
    }

    public function getWebsite(): ?string
    {
        return $this->getOrganization()->getWebsite();
    }

    public function getCountry(): ?Country
    {
        return $this->getOrganization()->getCountry();
    }

    public function getAddress(): ?Address
    {
        return $this->getOrganization()->getAddress();
    }
}

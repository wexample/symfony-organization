<?php

namespace Wexample\SymfonyOrganization\Service;

use Doctrine\ORM\EntityManagerInterface;
use Wexample\SymfonyGeo\Entity\Country;
use Wexample\SymfonyOrganization\Entity\Organization;
use Wexample\SymfonyOrganization\Repository\OrganizationRepository;
use Wexample\SymfonyOrganization\Service\Identifier\LegalIdentifierSchemeRegistry;

/**
 * Adding to the directory without adding twice: an organization already
 * known by its VAT number, or by its country's legal identifier, is found
 * rather than created again.
 */
class OrganizationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OrganizationRepository $organizationRepository,
        private readonly LegalIdentifierSchemeRegistry $schemeRegistry,
    ) {
    }

    /**
     * Persisted, not flushed: the caller saves it with what points to it.
     */
    public function create(
        string $name,
        ?Country $country = null,
        ?string $legalIdentifier = null,
        ?string $vatNumber = null,
    ): Organization {
        $organization = (new Organization())
            ->setName($name)
            ->setCountry($country)
            ->setVatNumber($vatNumber);

        if (null !== $legalIdentifier) {
            $organization->setLegalIdentifier(
                $this->schemeRegistry->forCountry($country?->getIsoAlpha2Code())->normalizeLegalIdentifier($legalIdentifier)
            );
        }

        $this->entityManager->persist($organization);

        return $organization;
    }

    /**
     * The organization of this VAT number or legal identifier, else a new one.
     */
    public function findOrCreate(
        string $name,
        ?Country $country = null,
        ?string $legalIdentifier = null,
        ?string $vatNumber = null,
    ): Organization {
        return $this->find($country, $legalIdentifier, $vatNumber)
            ?? $this->create($name, $country, $legalIdentifier, $vatNumber);
    }

    public function find(
        ?Country $country = null,
        ?string $legalIdentifier = null,
        ?string $vatNumber = null,
    ): ?Organization {
        if ($vatNumber) {
            $vatNumber = strtoupper(preg_replace('/[\s.\-]/', '', $vatNumber));

            if ($organization = $this->organizationRepository->findOneByVatNumber($vatNumber)) {
                return $organization;
            }
        }

        if ($legalIdentifier && $country) {
            $code = $country->getIsoAlpha2Code();
            $normalized = $this->schemeRegistry->forCountry($code)->normalizeLegalIdentifier($legalIdentifier);

            return $this->organizationRepository->findOneByLegalIdentifier($code, $normalized);
        }

        return null;
    }
}

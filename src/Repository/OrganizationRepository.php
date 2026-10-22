<?php

namespace Wexample\SymfonyOrganization\Repository;

use Wexample\SymfonyHelpers\Repository\AbstractRepository;
use Wexample\SymfonyOrganization\Entity\Organization;

/**
 * @method Organization|null find($id, $lockMode = null, $lockVersion = null)
 * @method Organization|null findOneBy(array $criteria, array $orderBy = null)
 * @method Organization[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganizationRepository extends AbstractRepository
{
    public static function getEntityClassName(): string
    {
        return Organization::class;
    }

    /**
     * @param string $vatNumber Normalized, as Organization::setVatNumber() stores it.
     */
    public function findOneByVatNumber(string $vatNumber): ?Organization
    {
        return $this->findOneBy(['vatNumber' => $vatNumber]);
    }

    /**
     * @param string $legalIdentifier Normalized by the country's scheme.
     */
    public function findOneByLegalIdentifier(
        string $countryCode,
        string $legalIdentifier
    ): ?Organization {
        return $this->createQueryBuilder('o')
            ->join('o.country', 'c')
            ->andWhere('c.isoAlpha2Code = :country')
            ->andWhere('o.legalIdentifier = :identifier')
            ->setParameter('country', strtoupper($countryCode))
            ->setParameter('identifier', $legalIdentifier)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByExternalReference(string $externalReference): ?Organization
    {
        return $this->findOneBy(['externalReference' => $externalReference]);
    }
}

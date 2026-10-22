<?php

namespace Wexample\SymfonyOrganization\Repository;

use DateTimeImmutable;
use Wexample\SymfonyGeo\Entity\Address;
use Wexample\SymfonyHelpers\Repository\AbstractRepository;
use Wexample\SymfonyOrganization\Entity\Establishment;
use Wexample\SymfonyOrganization\Entity\Organization;

/**
 * @method Establishment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Establishment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Establishment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Establishment      saveNewEstablishment(Organization $organization, ?string $legalIdentifier = null, ?Address $address = null, bool $headOffice = false, ?DateTimeImmutable $dateOpened = null, ?DateTimeImmutable $dateClosed = null)
 */
class EstablishmentRepository extends AbstractRepository
{
    public static function getEntityClassName(): string
    {
        return Establishment::class;
    }

    /**
     * One of `$organization`'s establishments, both sides of the relation set.
     */
    public function createNewEstablishment(
        Organization $organization,
        ?string $legalIdentifier = null,
        ?Address $address = null,
        bool $headOffice = false,
        ?DateTimeImmutable $dateOpened = null,
        ?DateTimeImmutable $dateClosed = null,
    ): Establishment {
        $establishment = (new Establishment())
            ->setLegalIdentifier($legalIdentifier)
            ->setAddress($address)
            ->setHeadOffice($headOffice)
            ->setDateOpened($dateOpened)
            ->setDateClosed($dateClosed);

        $organization->addEstablishment($establishment);

        return $establishment;
    }
}

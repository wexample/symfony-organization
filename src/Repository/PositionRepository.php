<?php

namespace Wexample\SymfonyOrganization\Repository;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Wexample\SymfonyHelpers\Repository\AbstractRepository;
use Wexample\SymfonyOrganization\Entity\Organization;
use Wexample\SymfonyOrganization\Entity\Position;
use Wexample\SymfonyPerson\Entity\Person;

/**
 * @method Position|null find($id, $lockMode = null, $lockVersion = null)
 * @method Position      saveNewPosition(Person $person, string $title, ?Organization $organization = null)
 */
class PositionRepository extends AbstractRepository
{
    public static function getEntityClassName(): string
    {
        return Position::class;
    }

    public function createNewPosition(
        Person $person,
        string $title,
        ?Organization $organization = null
    ): Position {
        return new Position($person, $title, $organization);
    }

    public function findOneByExternalReference(string $externalReference): ?Position
    {
        return $this->findOneBy(['externalReference' => $externalReference]);
    }

    /**
     * Their positions, the latest first, the undated last.
     *
     * @return list<Position>
     */
    public function findByPerson(Person $person): array
    {
        // DQL has no NULLS LAST, and PostgreSQL puts nulls first in a descending order.
        return $this->createQueryBuilder('position')
            ->addSelect('CASE WHEN position.dateStart IS NULL THEN 1 ELSE 0 END AS HIDDEN undated')
            ->where('position.person = :person')
            ->setParameter('person', $person->getId(), UuidType::NAME)
            ->orderBy('undated', 'ASC')
            ->addOrderBy('position.dateStart', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * What $person held on $date: the capacities they could speak in that day.
     *
     * @return list<Position>
     */
    public function findHeldAt(
        Person $person,
        DateTimeInterface $date
    ): array {
        return $this->createQueryBuilder('position')
            ->where('position.person = :person')
            ->andWhere('position.dateStart IS NULL OR position.dateStart <= :date')
            ->andWhere('position.dateEnd IS NULL OR position.dateEnd >= :date')
            ->setParameter('person', $person->getId(), UuidType::NAME)
            ->setParameter('date', $date, Types::DATE_IMMUTABLE)
            ->getQuery()
            ->getResult();
    }

    /**
     * Who held a position there, the earliest first.
     *
     * @return list<Position>
     */
    public function findByOrganization(Organization $organization): array
    {
        return $this->createQueryBuilder('position')
            ->where('position.organization = :organization')
            ->setParameter('organization', $organization->getId(), UuidType::NAME)
            ->orderBy('position.dateStart', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

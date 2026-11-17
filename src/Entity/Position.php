<?php

namespace Wexample\SymfonyOrganization\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyOrganization\Repository\PositionRepository;
use Wexample\SymfonyPerson\Entity\Person;

/**
 * A position someone held, over a period: member of parliament for a term,
 * minister of justice in a government, president of the Republic, journalist at
 * a newsroom. Public, dated, and kept once over: a person's career is the list of
 * theirs.
 *
 * Neither a Contact — someone of an organization's private address book — nor an
 * OrganizationMembership — an account's rights in it.
 */
#[ORM\Entity(repositoryClass: PositionRepository::class)]
#[ORM\Table(name: 'organization_position')]
#[ORM\Index(columns: ['date_start'])]
class Position extends AbstractEntity implements Stringable
{
    #[ORM\ManyToOne(targetEntity: Person::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    protected Person $person;

    /**
     * Where it is held; null for a position that is its own institution's only
     * one, told by its title alone.
     */
    #[ORM\ManyToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    protected ?Organization $organization;

    /**
     * As it is said: « Garde des sceaux, ministre de la Justice », « Député ».
     */
    #[ORM\Column(type: Types::STRING, length: 255)]
    protected string $title;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    protected ?DateTimeImmutable $dateStart = null;

    /** Null while it is held. */
    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    protected ?DateTimeImmutable $dateEnd = null;

    /**
     * Its key where it was imported from (`wikidata:Q…`, `an:…`): an import finds
     * by it what it already brought.
     */
    #[ORM\Column(type: Types::STRING, length: 255, unique: true, nullable: true)]
    protected ?string $externalReference = null;

    public function __construct(
        Person $person,
        string $title,
        ?Organization $organization = null
    ) {
        parent::__construct();

        $this->person = $person;
        $this->title = $title;
        $this->organization = $organization;
    }

    public function __toString(): string
    {
        return null === $this->organization ? $this->title : $this->title.' ('.$this->organization.')';
    }

    public function getPerson(): Person
    {
        return $this->person;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDateStart(): ?DateTimeImmutable
    {
        return $this->dateStart;
    }

    public function getDateEnd(): ?DateTimeImmutable
    {
        return $this->dateEnd;
    }

    public function setPeriod(
        ?DateTimeImmutable $dateStart,
        ?DateTimeImmutable $dateEnd
    ): static {
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Whether it was held on $date. An unknown start or end does not exclude it.
     */
    public function isActiveAt(DateTimeInterface $date): bool
    {
        return (null === $this->dateStart || $this->dateStart <= $date)
            && (null === $this->dateEnd || $this->dateEnd >= $date);
    }

    public function getExternalReference(): ?string
    {
        return $this->externalReference;
    }

    public function setExternalReference(?string $externalReference): static
    {
        $this->externalReference = $externalReference;

        return $this;
    }
}

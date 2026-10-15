<?php

namespace Wexample\SymfonyOrganization\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Wexample\Pseudocode\Attribute\PseudocodeExport;
use Wexample\SymfonyGeo\Entity\Address;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyOrganization\Interface\HasOrganizationInterface;
use Wexample\SymfonyOrganization\Repository\EstablishmentRepository;

/**
 * A place an organization works from, as the register knows it: its own
 * identifier (a French SIRET, a Belgian establishment unit number), its
 * address, when it opened and closed, whether it is the head office. An
 * organization moving opens a new establishment and closes the old one: its
 * past addresses stay, dated.
 */
#[PseudocodeExport(inherited: true)]
#[ORM\Entity(repositoryClass: EstablishmentRepository::class)]
#[ORM\Table(name: 'organization_establishment')]
#[ORM\Index(columns: ['legal_identifier'])]
class Establishment extends AbstractEntity implements HasOrganizationInterface
{
    #[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: 'establishments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    protected Organization $organization;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    protected ?string $legalIdentifier = null;

    #[ORM\ManyToOne(targetEntity: Address::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    protected ?Address $address = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    protected bool $headOffice = false;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    protected ?DateTimeImmutable $dateOpened = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    protected ?DateTimeImmutable $dateClosed = null;

    public function __toString(): string
    {
        return (string) ($this->legalIdentifier ?? $this->address?->getCity() ?? '');
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function setOrganization(Organization $organization): static
    {
        $this->organization = $organization;

        return $this;
    }

    public function getLegalIdentifier(): ?string
    {
        return $this->legalIdentifier;
    }

    public function setLegalIdentifier(?string $legalIdentifier): static
    {
        $this->legalIdentifier = $legalIdentifier;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function isHeadOffice(): bool
    {
        return $this->headOffice;
    }

    public function setHeadOffice(bool $headOffice): static
    {
        $this->headOffice = $headOffice;

        return $this;
    }

    public function getDateOpened(): ?DateTimeImmutable
    {
        return $this->dateOpened;
    }

    public function setDateOpened(?DateTimeImmutable $dateOpened): static
    {
        $this->dateOpened = $dateOpened;

        return $this;
    }

    public function getDateClosed(): ?DateTimeImmutable
    {
        return $this->dateClosed;
    }

    public function setDateClosed(?DateTimeImmutable $dateClosed): static
    {
        $this->dateClosed = $dateClosed;

        return $this;
    }

    /**
     * Open on that day: opened on or before it, not closed before it.
     */
    public function isOpenAt(DateTimeInterface $date): bool
    {
        $day = DateTimeImmutable::createFromInterface($date)->setTime(0, 0);

        return (null === $this->dateOpened || $this->dateOpened <= $day)
            && (null === $this->dateClosed || $this->dateClosed > $day);
    }
}

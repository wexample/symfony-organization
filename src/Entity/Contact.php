<?php

namespace Wexample\SymfonyOrganization\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Wexample\Pseudocode\Attribute\PseudocodeExport;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyOrganization\Interface\HasOrganizationInterface;
use Wexample\SymfonyOrganization\Repository\ContactRepository;
use Wexample\SymfonySearch\Attribute\Searchable;
use Wexample\SymfonySearch\Attribute\SearchEmail;
use Wexample\SymfonySearch\Attribute\SearchText;

/**
 * Someone at an organization of the directory — its manager, its legal
 * representative, the person its invoices are addressed to —: one entry of
 * its address book. A person, not an account: a contact may never log in.
 */
#[PseudocodeExport(inherited: true)]
#[ORM\Entity(repositoryClass: ContactRepository::class)]
#[ORM\Table(name: 'organization_contact')]
#[Searchable(
    fields: ['name' => new SearchText(), 'email' => new SearchEmail()],
    titleField: 'name',
)]
class Contact extends AbstractEntity implements HasOrganizationInterface
{
    #[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: 'contacts')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    protected Organization $organization;

    #[ORM\Column(type: Types::STRING, length: 255)]
    protected string $name;

    /** What they are there: "Gérant", "Représentant légal", "Comptabilité". */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $jobTitle = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $email = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    protected ?string $phone = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $notes = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    protected DateTimeImmutable $dateCreated;

    public function __construct()
    {
        parent::__construct();

        $this->dateCreated = new DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->name;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function setJobTitle(?string $jobTitle): static
    {
        $this->jobTitle = $jobTitle;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getDateCreated(): DateTimeImmutable
    {
        return $this->dateCreated;
    }
}

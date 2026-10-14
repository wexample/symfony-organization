<?php

namespace Wexample\SymfonyOrganization\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Wexample\Pseudocode\Attribute\PseudocodeExport;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyOrganization\Entity\Traits\HasLegalIdentityTrait;
use Wexample\SymfonyOrganization\Interface\LegalIdentityInterface;
use Wexample\SymfonyOrganization\Repository\OrganizationRepository;
use Wexample\SymfonyOrganization\Validator\Constraint\ValidLegalIdentity;
use Wexample\SymfonySearch\Attribute\Searchable;
use Wexample\SymfonySearch\Attribute\SearchEmail;
use Wexample\SymfonySearch\Attribute\SearchText;

/**
 * An organization of the directory — a company, a sole trader, an
 * association, a private person —, once, whatever points to it: the ledger
 * keeping its books, the parties it is to other ledgers, its projects, its
 * mails. Its identity is changed here, for all of them; documents already
 * issued keep the copy they froze.
 */
#[PseudocodeExport(inherited: true)]
#[ORM\Entity(repositoryClass: OrganizationRepository::class)]
#[ORM\Table(name: 'organization')]
#[ORM\Index(columns: ['vat_number'])]
#[ORM\Index(columns: ['legal_identifier'])]
#[Searchable(
    fields: ['name' => new SearchText(), 'vatNumber' => new SearchText(), 'legalIdentifier' => new SearchText(), 'email' => new SearchEmail()],
    titleField: 'name',
)]
#[ValidLegalIdentity]
class Organization extends AbstractEntity implements LegalIdentityInterface
{
    use HasLegalIdentityTrait;

    /** The host's key for it, when it comes from another system (an import, a CRM). */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true)]
    protected ?string $externalReference = null;

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

    public function getExternalReference(): ?string
    {
        return $this->externalReference;
    }

    public function setExternalReference(?string $externalReference): static
    {
        $this->externalReference = $externalReference;

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

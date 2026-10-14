<?php

namespace Wexample\SymfonyOrganization\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
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
 *
 * It keeps an address book of the people met there (Contact), one of whom
 * its invoices are addressed to.
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

    /** @var Collection<int, Contact> Its address book. */
    #[ORM\OneToMany(targetEntity: Contact::class, mappedBy: 'organization', cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['name' => 'ASC'])]
    protected Collection $contacts;

    /** Whom its invoices are addressed to, one of its own contacts. */
    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    protected ?Contact $billingContact = null;

    public function __construct()
    {
        parent::__construct();

        $this->dateCreated = new DateTimeImmutable();
        $this->contacts = new ArrayCollection();
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

    /**
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): static
    {
        if (! $this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->setOrganization($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): static
    {
        $this->contacts->removeElement($contact);

        if ($this->billingContact === $contact) {
            $this->billingContact = null;
        }

        return $this;
    }

    public function getBillingContact(): ?Contact
    {
        return $this->billingContact;
    }

    /**
     * @throws InvalidArgumentException For a contact of another organization:
     *                                  invoices go to someone of this one.
     */
    public function setBillingContact(?Contact $billingContact): static
    {
        if ($billingContact && ! $this->contacts->contains($billingContact)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a contact of "%s".', $billingContact, $this));
        }

        $this->billingContact = $billingContact;

        return $this;
    }
}

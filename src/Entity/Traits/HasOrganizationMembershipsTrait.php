<?php

namespace Wexample\SymfonyOrganization\Entity\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Wexample\SymfonyOrganization\Entity\Organization;
use Wexample\SymfonyOrganization\Entity\OrganizationMembership;

/**
 * The account's side of OrganizationMembership, for the application's user
 * class — the one UserEntityInterface resolves to. It does not implement
 * MemberInterface::getMemberships(): the class adds these memberships to the
 * others it holds (teams, projects).
 */
trait HasOrganizationMembershipsTrait
{
    /** @var Collection<int, OrganizationMembership>|null */
    #[ORM\OneToMany(targetEntity: OrganizationMembership::class, mappedBy: 'user', cascade: ['persist'], orphanRemoval: true)]
    protected ?Collection $organizationMemberships = null;

    /**
     * @return Collection<int, OrganizationMembership>
     */
    public function getOrganizationMemberships(): Collection
    {
        // Built on first use: a trait has no constructor of its own.
        return $this->organizationMemberships ??= new ArrayCollection();
    }

    public function addOrganizationMembership(OrganizationMembership $membership): static
    {
        if (! $this->getOrganizationMemberships()->contains($membership)) {
            $this->getOrganizationMemberships()->add($membership);
        }

        return $this;
    }

    public function removeOrganizationMembership(OrganizationMembership $membership): static
    {
        $this->getOrganizationMemberships()->removeElement($membership);

        return $this;
    }

    /**
     * @return list<Organization>
     */
    public function getOrganizations(): array
    {
        return $this->getOrganizationMemberships()
            ->map(static fn (OrganizationMembership $membership) => $membership->getOrganization())
            ->getValues();
    }
}

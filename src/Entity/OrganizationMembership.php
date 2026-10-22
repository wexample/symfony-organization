<?php

namespace Wexample\SymfonyOrganization\Entity;

use Doctrine\ORM\Mapping as ORM;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Entity\Interfaces\UserEntityInterface;
use Wexample\SymfonyOrganization\Repository\OrganizationMembershipRepository;
use Wexample\SymfonyUser\Entity\Traits\HasGrantsTrait;
use Wexample\SymfonyUser\Interface\MemberInterface;
use Wexample\SymfonyUser\Interface\MembershipGroupInterface;
use Wexample\SymfonyUser\Interface\MembershipInterface;

/**
 * An account belonging to an organization of the directory, with its grants
 * there (`owner` by default, see the bundle's extension) — symfony-user's
 * membership, the organization being the group.
 *
 * The account is the application's own class: the package points at
 * UserEntityInterface, which the application resolves to it
 * (doctrine.orm.resolve_target_entities), and that class uses
 * HasOrganizationMembershipsTrait for the other side.
 */
#[ORM\Entity(repositoryClass: OrganizationMembershipRepository::class)]
#[ORM\Table(name: 'organization_membership')]
#[ORM\UniqueConstraint(columns: ['organization_id', 'user_id'])]
class OrganizationMembership extends AbstractEntity implements MembershipInterface
{
    use HasGrantsTrait;

    #[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: 'memberships')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    protected Organization $organization;

    #[ORM\ManyToOne(targetEntity: UserEntityInterface::class, inversedBy: 'organizationMemberships')]
    #[ORM\JoinColumn(name: 'user_id', nullable: false, onDelete: 'CASCADE')]
    protected UserEntityInterface $user;

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function setOrganization(Organization $organization): static
    {
        $this->organization = $organization;

        return $this;
    }

    public function getUser(): UserEntityInterface
    {
        return $this->user;
    }

    public function setUser(UserEntityInterface $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * The account, which the application's class makes a member by
     * implementing MemberInterface.
     */
    public function getMember(): MemberInterface
    {
        return $this->user;
    }

    public function getGroup(): MembershipGroupInterface
    {
        return $this->organization;
    }
}

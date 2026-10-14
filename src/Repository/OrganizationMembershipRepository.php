<?php

namespace Wexample\SymfonyOrganization\Repository;

use Wexample\SymfonyHelpers\Entity\Interfaces\UserEntityInterface;
use Wexample\SymfonyHelpers\Repository\AbstractRepository;
use Wexample\SymfonyOrganization\Entity\Organization;
use Wexample\SymfonyOrganization\Entity\OrganizationMembership;

/**
 * @method OrganizationMembership|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrganizationMembership|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrganizationMembership[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method OrganizationMembership      saveNewOrganizationMembership(Organization $organization, UserEntityInterface $user, array $grants = ['owner'])
 */
class OrganizationMembershipRepository extends AbstractRepository
{
    public static function getEntityClassName(): string
    {
        return OrganizationMembership::class;
    }

    /**
     * Linked on both sides; `$user` is the application's class, using
     * HasOrganizationMembershipsTrait.
     *
     * @param list<string> $grants
     */
    public function createNewOrganizationMembership(
        Organization $organization,
        UserEntityInterface $user,
        array $grants = ['owner'],
    ): OrganizationMembership {
        $membership = (new OrganizationMembership())
            ->setOrganization($organization)
            ->setUser($user)
            ->setGrants($grants);

        $organization->addMembership($membership);
        $user->addOrganizationMembership($membership);

        return $membership;
    }
}

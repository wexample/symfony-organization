<?php

namespace Wexample\SymfonyOrganization\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wexample\SymfonyOrganization\Entity\Organization;
use Wexample\SymfonyOrganization\Entity\OrganizationMembership;
use Wexample\SymfonyUser\Interface\MembershipGroupInterface;

class OrganizationMembershipTest extends TestCase
{
    public function testAnOrganizationIsAGroupOfMemberships(): void
    {
        $organization = (new Organization())->setName('Wexample');
        $membership = (new OrganizationMembership())->setOrganization($organization)->setGrants(['owner', 'owner']);

        $organization->addMembership($membership)->addMembership($membership);

        $this->assertInstanceOf(MembershipGroupInterface::class, $organization);
        $this->assertSame('Wexample', $organization->getMembershipLabel());
        $this->assertCount(1, $organization->getMemberships());
        $this->assertSame($organization, $membership->getGroup());
        $this->assertSame(['owner'], $membership->getGrants());

        $organization->removeMembership($membership);
        $this->assertCount(0, $organization->getMemberships());
    }
}

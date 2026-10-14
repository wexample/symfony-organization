<?php

namespace Wexample\SymfonyOrganization\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wexample\SymfonyOrganization\Entity\Organization;

class OrganizationTest extends TestCase
{
    public function testIdentity(): void
    {
        $organization = (new Organization())
            ->setName('Acme SRL')
            ->setVatNumber('be 0123.456-749')
            ->setLegalForm('SRL');

        $this->assertSame('Acme SRL', (string) $organization);
        $this->assertSame('BE0123456749', $organization->getVatNumber());
        $this->assertTrue($organization->hasVatNumber());
        $this->assertFalse($organization->isIndividual());
        $this->assertFalse($organization->setVatNumber(null)->hasVatNumber());
        $this->assertNotNull($organization->getDateCreated());
    }
}

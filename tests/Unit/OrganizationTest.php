<?php

namespace Wexample\SymfonyOrganization\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wexample\SymfonyGeo\Entity\Address;
use Wexample\SymfonyGeo\Entity\Country;
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

    /**
     * The address is shared, so a move is written on it and read from every
     * identity pointing at it; the country of the identity is its own, the
     * one it is registered in.
     */
    public function testAddressIsAPointedRow(): void
    {
        $address = (new Address())->setPostalAddress('Rue Neuve 1')->setPostCode('1000')->setCity('Bruxelles');
        $organization = (new Organization())->setName('Acme SRL')->setCountry((new Country())->setIsoAlpha2Code('BE'));

        $this->assertNull($organization->getAddress());

        $organization->setAddress($address);
        $address->setPostalAddress('Rue Neuve 2');

        $this->assertSame('Rue Neuve 2', $organization->getAddress()->getPostalAddress());
        $this->assertSame('BE', $organization->getCountry()->getIsoAlpha2Code());
        $this->assertNull($organization->getAddress()->getCountry());
    }
}

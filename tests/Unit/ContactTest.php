<?php

namespace Wexample\SymfonyOrganization\Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Wexample\SymfonyOrganization\Entity\Contact;
use Wexample\SymfonyOrganization\Entity\Organization;

class ContactTest extends TestCase
{
    public function testAnOrganizationKeepsItsAddressBook(): void
    {
        $organization = (new Organization())->setName('The Photo Academy SA');
        $contact = (new Contact())->setName('Jane Doe')->setJobTitle('Legal representative');

        $organization->addContact($contact)->addContact($contact);

        $this->assertCount(1, $organization->getContacts());
        $this->assertSame($organization, $contact->getOrganization());

        $organization->setBillingContact($contact);
        $this->assertSame($contact, $organization->getBillingContact());

        // Removed from the book, it is no longer whom invoices go to.
        $organization->removeContact($contact);
        $this->assertNull($organization->getBillingContact());
    }

    public function testInvoicesGoToSomeoneOfTheOrganization(): void
    {
        $other = (new Organization())->setName('Other SRL');
        $stranger = (new Contact())->setName('John Roe');
        $other->addContact($stranger);

        $this->expectException(InvalidArgumentException::class);

        (new Organization())->setName('Acme SRL')->setBillingContact($stranger);
    }
}

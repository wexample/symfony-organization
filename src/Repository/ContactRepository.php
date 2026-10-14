<?php

namespace Wexample\SymfonyOrganization\Repository;

use Wexample\SymfonyHelpers\Repository\AbstractRepository;
use Wexample\SymfonyOrganization\Entity\Contact;
use Wexample\SymfonyOrganization\Entity\Organization;

/**
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Contact      saveNewContact(Organization $organization, string $name, ?string $jobTitle = null, ?string $email = null, ?string $phone = null)
 */
class ContactRepository extends AbstractRepository
{
    public static function getEntityClassName(): string
    {
        return Contact::class;
    }

    /**
     * In the address book of `$organization`, both sides of the relation set.
     */
    public function createNewContact(
        Organization $organization,
        string $name,
        ?string $jobTitle = null,
        ?string $email = null,
        ?string $phone = null,
    ): Contact {
        $contact = (new Contact())
            ->setName($name)
            ->setJobTitle($jobTitle)
            ->setEmail($email)
            ->setPhone($phone);

        $organization->addContact($contact);

        return $contact;
    }
}

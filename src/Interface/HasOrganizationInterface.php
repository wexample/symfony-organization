<?php

namespace Wexample\SymfonyOrganization\Interface;

use Wexample\SymfonyOrganization\Entity\Organization;

/**
 * Something that is, or acts for, an organization of the directory: a ledger
 * keeping its books, a party it trades with.
 */
interface HasOrganizationInterface
{
    public function getOrganization(): Organization;
}

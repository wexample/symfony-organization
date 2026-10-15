<?php

namespace Wexample\SymfonyOrganization\Tests\Unit;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Wexample\SymfonyOrganization\Entity\Establishment;
use Wexample\SymfonyOrganization\Entity\Organization;

class EstablishmentTest extends TestCase
{
    public function testAMoveKeepsThePastAddressDated(): void
    {
        $organization = (new Organization())->setName('Sole trader');
        $reims = (new Establishment())->setLegalIdentifier('79163664000017')->setHeadOffice(true)
            ->setDateOpened(new DateTimeImmutable('2013-03-05'))->setDateClosed(new DateTimeImmutable('2018-01-01'));
        $soissons = (new Establishment())->setLegalIdentifier('79163664000025')->setHeadOffice(true)
            ->setDateOpened(new DateTimeImmutable('2018-01-01'))->setDateClosed(new DateTimeImmutable('2026-01-01'));

        $organization->addEstablishment($reims)->addEstablishment($soissons)->addEstablishment($reims);

        $this->assertCount(2, $organization->getEstablishments());
        $this->assertSame($organization, $soissons->getOrganization());
        $this->assertSame($reims, $organization->getHeadOffice(new DateTimeImmutable('2017-12-31')));
        $this->assertSame($soissons, $organization->getHeadOffice(new DateTimeImmutable('2018-01-01')));
        // Everything closed: the last one is still where it was.
        $this->assertSame($soissons, $organization->getHeadOffice(new DateTimeImmutable('2026-10-15')));
        $this->assertFalse($soissons->isOpenAt(new DateTimeImmutable('2026-01-01')));
    }
}

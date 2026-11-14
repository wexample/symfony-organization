<?php

declare(strict_types=1);

namespace Wexample\SymfonyOrganization\Tests\Unit;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Wexample\SymfonyGeo\Entity\Country;
use Wexample\SymfonyOrganization\Entity\Organization;
use Wexample\SymfonyOrganization\Repository\OrganizationRepository;
use Wexample\SymfonyOrganization\Service\Identifier\BeLegalIdentifierScheme;
use Wexample\SymfonyOrganization\Service\Identifier\FrLegalIdentifierScheme;
use Wexample\SymfonyOrganization\Service\Identifier\LegalIdentifierSchemeRegistry;
use Wexample\SymfonyOrganization\Service\OrganizationService;

/**
 * An organization is found by its VAT number as it is kept, else by its
 * country's legal identifier as that country's scheme normalizes it.
 */
class OrganizationServiceTest extends TestCase
{
    public function testAVatNumberIsLookedForNormalized(): void
    {
        $known = new Organization();
        $repository = $this->createMock(OrganizationRepository::class);
        $repository->expects($this->once())->method('findOneByVatNumber')->with('BE0123456749')->willReturn($known);
        $repository->expects($this->never())->method('findOneByLegalIdentifier');

        $this->assertSame($known, $this->service($repository)->find($this->country('BE'), '0123.456.749', 'be 0123.456.749'));
    }

    public function testAnUnknownVatNumberFallsBackToTheLegalIdentifier(): void
    {
        $known = new Organization();
        $repository = $this->createMock(OrganizationRepository::class);
        $repository->expects($this->once())->method('findOneByVatNumber')->with('FR44732829320')->willReturn(null);
        $repository->expects($this->once())->method('findOneByLegalIdentifier')->with('FR', '732829320')->willReturn($known);

        $this->assertSame($known, $this->service($repository)->find($this->country('FR'), '732 829 320', 'FR 44 732 829 320'));
    }

    public function testALegalIdentifierNeedsItsCountry(): void
    {
        $repository = $this->createMock(OrganizationRepository::class);
        $repository->expects($this->never())->method('findOneByVatNumber');
        $repository->expects($this->never())->method('findOneByLegalIdentifier');

        $service = $this->service($repository);

        $this->assertNull($service->find(null, '732 829 320'));
        $this->assertNull($service->find());
        $this->assertNull($service->find($this->country('FR'), '', ''));
    }

    public function testFindOrCreateCreatesWhatIsNotFound(): void
    {
        $repository = $this->createMock(OrganizationRepository::class);
        $repository->expects($this->once())->method('findOneByVatNumber')->willReturn(null);
        $repository->expects($this->once())->method('findOneByLegalIdentifier')->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())->method('persist')->with($this->isInstanceOf(Organization::class));

        $organization = (new OrganizationService($entityManager, $repository, $this->registry()))
            ->findOrCreate('Acme SRL', $this->country('BE'), 'BE 0123.456.749', 'be 0123.456.749');

        $this->assertSame('Acme SRL', $organization->getName());
        $this->assertSame('BE0123456749', $organization->getVatNumber());
        $this->assertSame('0123456749', $organization->getLegalIdentifier());
    }

    private function service(OrganizationRepository $repository): OrganizationService
    {
        return new OrganizationService($this->createStub(EntityManagerInterface::class), $repository, $this->registry());
    }

    private function registry(): LegalIdentifierSchemeRegistry
    {
        return new LegalIdentifierSchemeRegistry([new FrLegalIdentifierScheme(), new BeLegalIdentifierScheme()]);
    }

    private function country(string $code): Country
    {
        return (new Country())->setIsoAlpha2Code($code);
    }
}

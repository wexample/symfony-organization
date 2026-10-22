<?php

namespace Wexample\SymfonyOrganization\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wexample\SymfonyGeo\Entity\Country;
use Wexample\SymfonyOrganization\Entity\Organization;
use Wexample\SymfonyOrganization\Service\Identifier\BeLegalIdentifierScheme;
use Wexample\SymfonyOrganization\Service\Identifier\DefaultLegalIdentifierScheme;
use Wexample\SymfonyOrganization\Service\Identifier\FrLegalIdentifierScheme;
use Wexample\SymfonyOrganization\Service\Identifier\LegalIdentifierSchemeRegistry;

class LegalIdentifierSchemeTest extends TestCase
{
    private function registry(): LegalIdentifierSchemeRegistry
    {
        return new LegalIdentifierSchemeRegistry([new FrLegalIdentifierScheme(), new BeLegalIdentifierScheme()]);
    }

    public function testRegistryFindsTheCountrySchemeOrTheGenericOne(): void
    {
        $registry = $this->registry();

        $this->assertInstanceOf(FrLegalIdentifierScheme::class, $registry->forCountry('fr'));
        $this->assertInstanceOf(BeLegalIdentifierScheme::class, $registry->forCountry('BE'));
        $this->assertInstanceOf(DefaultLegalIdentifierScheme::class, $registry->forCountry('DE'));
        $this->assertInstanceOf(DefaultLegalIdentifierScheme::class, $registry->forCountry(null));

        $organization = (new Organization())->setName('Acme SRL')->setCountry((new Country())->setIsoAlpha2Code('BE'));
        $this->assertInstanceOf(BeLegalIdentifierScheme::class, $registry->forIdentity($organization));
    }

    public function testFrance(): void
    {
        $scheme = new FrLegalIdentifierScheme();

        $this->assertTrue($scheme->isLegalIdentifierValid('732 829 320'));
        $this->assertTrue($scheme->isLegalIdentifierValid('732 829 320 00009'));
        $this->assertFalse($scheme->isLegalIdentifierValid('732829321'));
        $this->assertSame('732829320', $scheme->normalizeLegalIdentifier('732 829 320'));
        $this->assertSame('732 829 320 00009', $scheme->formatLegalIdentifier('73282932000009'));
        $this->assertSame('FR44732829320', $scheme->buildVatNumber('73282932000009'));
        $this->assertFalse($scheme->isLegalIdentifierIncludedInVatNumber());
        $this->assertTrue($scheme->isVatNumberValid('FR44732829320'));
        $this->assertFalse($scheme->isVatNumberValid('FR45732829320'));
        // Another country's number follows the generic rules.
        $this->assertTrue($scheme->isVatNumberValid('BE0123456749'));
    }

    public function testBelgium(): void
    {
        $scheme = new BeLegalIdentifierScheme();

        $this->assertTrue($scheme->isLegalIdentifierValid('0123.456.749'));
        $this->assertFalse($scheme->isLegalIdentifierValid('0123.456.748'));
        $this->assertSame('0123456749', $scheme->normalizeLegalIdentifier('BE 0123.456.749'));
        $this->assertSame('0123.456.749', $scheme->formatLegalIdentifier('0123456749'));
        $this->assertSame('BE0123456749', $scheme->buildVatNumber('0123.456.749'));
        $this->assertNull($scheme->buildVatNumber('0123.456.748'));
        $this->assertTrue($scheme->isLegalIdentifierIncludedInVatNumber());
        $this->assertFalse($scheme->isVatNumberValid('BE0123456748'));
    }

    public function testGenericRules(): void
    {
        $scheme = new DefaultLegalIdentifierScheme();

        $this->assertSame('identity.legal_identifier', $scheme->getLegalIdentifierLabel());
        $this->assertTrue($scheme->isLegalIdentifierValid('HRB 12345'));
        $this->assertFalse($scheme->isLegalIdentifierValid('  '));
        $this->assertTrue($scheme->isVatNumberValid('DE 123 456 789'));
        $this->assertFalse($scheme->isVatNumberValid('123456789'));
        $this->assertNull($scheme->buildVatNumber('HRB 12345'));
    }
}

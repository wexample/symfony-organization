<?php

declare(strict_types=1);

namespace Wexample\SymfonyOrganization\Tests\Unit;

use stdClass;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Wexample\SymfonyGeo\Entity\Country;
use Wexample\SymfonyOrganization\Entity\Organization;
use Wexample\SymfonyOrganization\Service\Identifier\BeLegalIdentifierScheme;
use Wexample\SymfonyOrganization\Service\Identifier\FrLegalIdentifierScheme;
use Wexample\SymfonyOrganization\Service\Identifier\LegalIdentifierSchemeRegistry;
use Wexample\SymfonyOrganization\Validator\Constraint\ValidLegalIdentity;
use Wexample\SymfonyOrganization\Validator\Constraint\ValidLegalIdentityValidator;

/**
 * A legal identity checked by the rules of its country, each field refused
 * on its own path.
 *
 * @extends ConstraintValidatorTestCase<ValidLegalIdentityValidator>
 */
class ValidLegalIdentityValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ValidLegalIdentityValidator
    {
        return new ValidLegalIdentityValidator(
            new LegalIdentifierSchemeRegistry([new FrLegalIdentifierScheme(), new BeLegalIdentifierScheme()])
        );
    }

    public function testAValidIdentityRaisesNothing(): void
    {
        $this->validator->validate($this->organization('BE', '0123.456.749', 'BE0123456749'), new ValidLegalIdentity());

        $this->assertNoViolation();
    }

    public function testAnEmptyIdentityRaisesNothing(): void
    {
        $this->validator->validate($this->organization('FR', null, null), new ValidLegalIdentity());

        $this->assertNoViolation();
    }

    public function testEachInvalidFieldIsRefusedOnItsPath(): void
    {
        $this->validator->validate($this->organization('BE', '0123.456.748', 'BE0123456748'), new ValidLegalIdentity());

        $this->buildViolation('identity.error.legal_identifier')
            ->setTranslationDomain('organization')
            ->atPath('property.path.legalIdentifier')
            ->buildNextViolation('identity.error.vat_number')
            ->setTranslationDomain('organization')
            ->atPath('property.path.vatNumber')
            ->assertRaised();
    }

    public function testTheRulesAreTheCountrysOfTheIdentity(): void
    {
        // A valid Belgian number, but a French identifier is checked as French.
        $this->validator->validate($this->organization('FR', '0123.456.749', null), new ValidLegalIdentity());

        $this->buildViolation('identity.error.legal_identifier')
            ->setTranslationDomain('organization')
            ->atPath('property.path.legalIdentifier')
            ->assertRaised();
    }

    public function testWhatIsNoLegalIdentityIsLeftAlone(): void
    {
        $this->validator->validate(new stdClass(), new ValidLegalIdentity());
        $this->validator->validate(null, new ValidLegalIdentity());

        $this->assertNoViolation();
    }

    public function testAnotherConstraintIsRefused(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate($this->organization('BE', null, null), new NotBlank());
    }

    private function organization(string $country, ?string $legalIdentifier, ?string $vatNumber): Organization
    {
        return (new Organization())
            ->setName('Acme')
            ->setCountry((new Country())->setIsoAlpha2Code($country))
            ->setLegalIdentifier($legalIdentifier)
            ->setVatNumber($vatNumber);
    }
}

# symfony-organization

Version: 3.0.0

## The directory

`Organization` (table `organization`) holds a legal identity, through `HasLegalIdentityTrait`: `name`, `legalIdentifier`, `vatNumber` (normalized: no space, dot nor dash, upper case), `legalForm`, `individual` (a private person: B2C rules, no VAT number), `email`, `phone`, `website`, `country` — the one it is registered in, which decides how it is identified and taxed — and `address`, a relation to the `address` table of `symfony-geo` (`Address`). The address is pointed at and never copied: a move is written on that row, once, for every identity reading it, and what must not move with it is frozen as an `AddressSnapshot` by the document issuing it. An identity can have a country without an address, which is what an import usually knows of it. `externalReference` keeps the key of another system it comes from (an import, a CRM); `notes` are free.

`OrganizationService` adds without adding twice:

```php
$acme = $organizationService->create('Acme SRL', $belgium, '0123.456.749', 'BE0123456749');  // persisted, not flushed
$same = $organizationService->find(vatNumber: 'BE 0123.456.749');                              // by VAT number
$same = $organizationService->find($belgium, '0123456749');                                     // by the country's identifier
$acme = $organizationService->findOrCreate('ACME', $belgium, vatNumber: 'BE0123456749');      // found, not created
```

A legal identifier is stored as its country normalizes it, and found only within that country.

## Pointing to an organization

An entity that is, or acts for, an organization implements `HasOrganizationInterface` (`getOrganization()`) and, to read as it, `LegalIdentityInterface` through `DelegatesLegalIdentityTrait`: its getters read the organization's. Its identity is changed on the organization, once for everyone pointing to it; documents that must not change afterwards copy it when issued. Validate the relation (`#[Assert\Valid]`) so an identity typed through the entity follows its country's rules.

## Address book

An organization keeps the people met there as `Contact` (table `organization_contact`): a name, a job title ("Gérant", "Représentant légal", "Comptabilité"), an email, a phone, notes. A contact is a person, not an account — it may never log in — and it belongs to one organization, deleted with it. `ContactRepository::saveNewContact($organization, 'Jane Doe', 'Représentant légal')` adds one to the book.

`Organization::getBillingContact()` is the one its invoices are addressed to; it must be one of its own contacts (`setBillingContact()` refuses another's), and removing it from the book unsets it.

## Establishments

The places an organization works and worked from, as the register knows them (`Establishment`, table `organization_establishment`): each its own identifier (a French SIRET — the organization's own `legalIdentifier` is then its SIREN —, a Belgian establishment unit number), its address, when it opened and closed, whether it is the head office. An organization that moves opens a new establishment and closes the old one, so its past addresses stay, dated:

```php
$establishmentRepository->saveNewEstablishment($organization, '79163664000025', $address, headOffice: true, dateOpened: new DateTimeImmutable('2018-01-01'));
$organization->getHeadOffice($date);   // the head office open that day, else the last one
```

The organization's `address` stays its current one; documents already issued keep the address they froze.

## Logo

`Organization::getLogo()` is a media of symfony-media's library (a person's picture, for an individual), `null` once that media is deleted. The organization pages of `symfony-organization-ds` show it.

## Members

The accounts belonging to an organization are symfony-user's memberships, the organization being the group (`MembershipGroupInterface`): `OrganizationMembership` (table `organization_membership`) links an account to it with grants, `owner` by default — a grant this bundle declares for symfony-user, beside the application's own. So `is_granted('owner', $organization)` and `MembershipService` answer for organizations as for any group.

The account is the application's class, which the package does not know. It points at `UserEntityInterface`, and the application wires two things:

```yaml
# config/packages/doctrine.yaml
doctrine:
  orm:
    resolve_target_entities:
      Wexample\SymfonyHelpers\Entity\Interfaces\UserEntityInterface: App\Entity\User
```

and its user class uses `HasOrganizationMembershipsTrait` (the other side of the relation, `getOrganizations()`) and implements `MemberInterface`, adding `getOrganizationMemberships()` to what `getMemberships()` yields. Without both, the mapping of `OrganizationMembership` fails to load. `OrganizationMembershipRepository::saveNewOrganizationMembership($organization, $user)` adds a member.

An organization can in turn be a member of a group of the application — a project counting organizations among its contributors —, an account then acting with their grants through `RepresentativeInterface`.

## Identifiers by country

A `LegalIdentifierSchemeInterface` per country names its identifiers (translation keys, domain `organization`) and validates them: `normalizeLegalIdentifier()`, `formatLegalIdentifier()`, `isLegalIdentifierValid()`, `isVatNumberValid()`, `isLegalIdentifierIncludedInVatNumber()` (BE: the VAT number contains the enterprise number, so a document prints one of them), `buildVatNumber()`. Shipped: France (`FrLegalIdentifierScheme`: SIREN/SIRET, the FR VAT key) and Belgium (`BeLegalIdentifierScheme`: the enterprise number, modulo 97); any other country gets the generic rules. A new country is one class implementing the interface — it is registered by autoconfiguration. `LegalIdentifierSchemeRegistry::forCountry('BE')` or `forIdentity($organization)` gives the scheme. The `#[ValidLegalIdentity]` class constraint, on `Organization`, refuses an identifier or a VAT number its country rejects. `FrIdentityHelper` and `BeIdentityHelper` are the plain static rules underneath.

## VIES

`VatNumberVerifier::isConfirmed($vatNumber)` asks VIES (`symfony-remote-vies`) whether a VAT number is valid; a VIES giving no verdict confirms nothing.

## Front

`Organization` is exported to TypeScript in `assets/` (`Entity/Organization.ts`, its repository, `data/entity/organization.json`), following the entity export pipeline of `symfony-api`. Screens editing an identity embed `LegalIdentityType` of `symfony-organization-ds`.

## Table of Contents

- [The directory](#the-directory)
- [Pointing to an organization](#pointing-to-an-organization)
- [Address book](#address-book)
- [Establishments](#establishments)
- [Logo](#logo)
- [Members](#members)
- [Identifiers by country](#identifiers-by-country)
- [VIES](#vies)
- [Front](#front)
- [Where things are](#where-things-are)
- [Decisions](#decisions)
- [Tests](#tests)
- [Integration in the Suite](#integration-in-the-suite)
- [Dependencies](#dependencies)
- [Versioning & Compatibility Policy](#versioning--compatibility-policy)
- [License](#license)
- [About us](#about-us)
- [Migration Notes](#migration-notes)

## Where things are

- `src/Entity/Organization.php` — the directory entry; `src/Entity/Traits/HasLegalIdentityTrait.php` its columns; `src/Entity/Traits/DelegatesLegalIdentityTrait.php` for entities reading an organization's identity as their own.
- `src/Interface/` — `LegalIdentityInterface` (what an identity answers), `HasOrganizationInterface`, `LegalIdentifierSchemeInterface`.
- `src/Service/Identifier/` — the schemes (`AbstractLegalIdentifierScheme`, `DefaultLegalIdentifierScheme`, one class per country) and `LegalIdentifierSchemeRegistry`, fed by the tag `wexample_symfony_organization.legal_identifier_scheme` (autoconfigured).
- `src/Service/OrganizationService.php` — create / find / findOrCreate; `src/Service/VatNumberVerifier.php` — VIES.
- `src/Validator/Constraint/` — `ValidLegalIdentity` and its validator.
- `src/Helper/` — `FrIdentityHelper`, `BeIdentityHelper`: static rules, no dependency.

## Decisions

- The legal identity was in `symfony-accounting` (on `Ledger` and `Party`, duplicated per ledger). It moved here in 2026-10 so that one organization exists once, whatever points to it (ledgers, parties, and later projects, mails, users).
- Country rules live in this package, one small class per country, rather than in `-fr`/`-be` packages: they are data and checksums, without dependency. Country packages of other domains (accounting) keep what is heavy (charts, tax forms).
- `EuHelper` (EU member states, VAT prefixes for intra-EU rules) stays in `symfony-accounting`: it decides VAT, not identity.

## Tests

`tests/Unit`: the helpers, the schemes and the registry, the entity. Persistence and deduplication are exercised by `symfony-accounting`'s `OrganizationDirectoryTest`, which has a database kernel.

## Integration in the Suite

This package is part of the Wexample Suite — a collection of high-quality, modular tools designed to work seamlessly together across multiple languages and environments.

### Related Packages

The suite includes packages for configuration management, file handling, prompts, and more. Each package can be used independently or as part of the integrated suite.

## Dependencies

- php: >=8.5
- doctrine/orm: ^3.0
- symfony/validator: ^7.4 || ^8.0
- wexample/symfony-helpers: >=17.0.0
- wexample/symfony-geo: >=7.0.0
- wexample/symfony-remote-vies: >=2.0.0
- wexample/symfony-search: >=4.2.0
- wexample/symfony-user: >=13.0.0
- wexample/php-pseudocode: >=2.2.0
- wexample/symfony-pseudocode: >=4.0.0
- wexample/symfony-media: >=3.0.0

## Versioning & Compatibility Policy

Wexample packages follow **Semantic Versioning** (SemVer):

- **MAJOR**: Breaking changes
- **MINOR**: New features, backward compatible
- **PATCH**: Bug fixes, backward compatible

We maintain backward compatibility within major versions and provide clear migration guides for breaking changes.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

Free to use in both personal and commercial projects.

## About us

[Wexample](https://wexample.com) stands as a cornerstone of the digital ecosystem — a collective of seasoned engineers, researchers, and creators driven by a relentless pursuit of technological excellence. More than a media platform, it has grown into a vibrant community where innovation meets craftsmanship, and where every line of code reflects a commitment to clarity, durability, and shared intelligence.

This packages suite embodies this spirit. Trusted by professionals and enthusiasts alike, it delivers a consistent, high-quality foundation for modern development — open, elegant, and battle-tested. Its reputation is built on years of collaboration, refinement, and rigorous attention to detail, making it a natural choice for those who demand both robustness and beauty in their tools.

Wexample cultivates a culture of mastery. Each package, each contribution carries the mark of a community that values precision, ethics, and innovation — a community proud to shape the future of digital craftsmanship.

Built and maintained by [Romain Weeger](https://weeger.io).

## Migration Notes

When upgrading between major versions, refer to the migration guides in the documentation.

Breaking changes are clearly documented with upgrade paths and examples.

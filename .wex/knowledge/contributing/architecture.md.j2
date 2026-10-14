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

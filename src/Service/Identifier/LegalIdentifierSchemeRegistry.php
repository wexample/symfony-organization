<?php

namespace Wexample\SymfonyOrganization\Service\Identifier;

use Wexample\SymfonyOrganization\Interface\LegalIdentifierSchemeInterface;
use Wexample\SymfonyOrganization\Interface\LegalIdentityInterface;

/**
 * The scheme of a country, or the generic one.
 */
class LegalIdentifierSchemeRegistry
{
    /** @var array<string, LegalIdentifierSchemeInterface> */
    private array $schemes = [];

    private readonly LegalIdentifierSchemeInterface $default;

    /**
     * @param iterable<LegalIdentifierSchemeInterface> $schemes
     */
    public function __construct(iterable $schemes = [])
    {
        foreach ($schemes as $scheme) {
            if ($code = $scheme->getCountryCode()) {
                $this->schemes[strtoupper($code)] = $scheme;
            }
        }

        $this->default = new DefaultLegalIdentifierScheme();
    }

    public function forCountry(?string $countryCode): LegalIdentifierSchemeInterface
    {
        return $this->schemes[strtoupper((string) $countryCode)] ?? $this->default;
    }

    public function forIdentity(LegalIdentityInterface $identity): LegalIdentifierSchemeInterface
    {
        return $this->forCountry($identity->getCountry()?->getIsoAlpha2Code());
    }

    /**
     * @return array<string, LegalIdentifierSchemeInterface> By country code.
     */
    public function all(): array
    {
        return $this->schemes;
    }
}

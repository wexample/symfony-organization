<?php

namespace Wexample\SymfonyOrganization\Service\Identifier;

/**
 * A country no scheme describes: any non-empty identifier, any well-formed
 * EU-style VAT number.
 */
class DefaultLegalIdentifierScheme extends AbstractLegalIdentifierScheme
{
    public function getCountryCode(): ?string
    {
        return null;
    }
}

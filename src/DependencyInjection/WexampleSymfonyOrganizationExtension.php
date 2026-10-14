<?php

namespace Wexample\SymfonyOrganization\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Wexample\SymfonyHelpers\DependencyInjection\AbstractWexampleSymfonyExtension;
use Wexample\SymfonyOrganization\Interface\LegalIdentifierSchemeInterface;

class WexampleSymfonyOrganizationExtension extends AbstractWexampleSymfonyExtension
{
    public const string TAG_LEGAL_IDENTIFIER_SCHEME = 'wexample_symfony_organization.legal_identifier_scheme';

    public function load(
        array $configs,
        ContainerBuilder $container
    ): void {
        // Implementing the interface is enough to be registered.
        $container
            ->registerForAutoconfiguration(LegalIdentifierSchemeInterface::class)
            ->addTag(self::TAG_LEGAL_IDENTIFIER_SCHEME);

        $this->loadConfig(
            __DIR__,
            $container
        );
    }
}

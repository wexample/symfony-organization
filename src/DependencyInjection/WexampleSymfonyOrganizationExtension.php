<?php

namespace Wexample\SymfonyOrganization\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Wexample\SymfonyHelpers\DependencyInjection\AbstractWexampleSymfonyExtension;
use Wexample\SymfonyOrganization\Interface\LegalIdentifierSchemeInterface;

class WexampleSymfonyOrganizationExtension extends AbstractWexampleSymfonyExtension implements PrependExtensionInterface
{
    public const string TAG_LEGAL_IDENTIFIER_SCHEME = 'wexample_symfony_organization.legal_identifier_scheme';

    /**
     * The grant an account holds in its organization (OrganizationMembership),
     * declared for symfony-user beside the application's own grants.
     */
    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('wexample_symfony_user', [
            'memberships' => ['grants' => ['owner' => []]],
        ]);
    }

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

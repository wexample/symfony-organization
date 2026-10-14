<?php

namespace Wexample\SymfonyOrganization;

use Wexample\SymfonyHelpers\Class\AbstractBundle;
use Wexample\SymfonyHelpers\Helper\BundleHelper;
use Wexample\SymfonyHelpers\Interface\LoaderBundleInterface;
use Wexample\SymfonyPseudocode\Interface\PseudocodeBundleInterface;

/**
 * The organizations an application deals with, once each: who they are,
 * legally. Other packages point to them (a ledger and its parties, a project,
 * a mail) instead of copying their identity. Organization is exported to
 * TypeScript in its assets, for the screens and the other packages' schemas
 * pointing to it.
 */
class WexampleSymfonyOrganizationBundle extends AbstractBundle implements LoaderBundleInterface, PseudocodeBundleInterface
{
    public static function getLoaderFrontPaths(): array
    {
        return [
            BundleHelper::getBundleCssAlias(static::class) => __DIR__.'/../assets/',
        ];
    }

    public static function getPseudocodeSourcePaths(): array
    {
        return [__DIR__.'/'];
    }
}

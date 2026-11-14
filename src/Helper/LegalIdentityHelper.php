<?php

namespace Wexample\SymfonyOrganization\Helper;

/**
 * What every legal identity number goes through before it is compared,
 * checked or kept, whichever country it is from.
 */
class LegalIdentityHelper
{
    /**
     * As typed — "be 0123.456.749", "FR-40 303 265 045" — to the characters
     * that make the number: spaces, dots and dashes dropped, letters uppercased.
     */
    public static function normalize(string $number): string
    {
        return strtoupper(preg_replace('/[\s.\-]/', '', $number));
    }
}

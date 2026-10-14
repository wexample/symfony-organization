<?php

namespace Wexample\SymfonyOrganization\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wexample\SymfonyOrganization\Helper\FrIdentityHelper;

class FrIdentityHelperTest extends TestCase
{
    public function testSirenAndSiret(): void
    {
        $this->assertTrue(FrIdentityHelper::isValidSiren('732 829 320'));
        $this->assertFalse(FrIdentityHelper::isValidSiren('732829321'));
        $this->assertTrue(FrIdentityHelper::isValidSiret('732 829 320 00009'));
        $this->assertFalse(FrIdentityHelper::isValidSiret('73282932000008'));
        $this->assertSame('732829320', FrIdentityHelper::sirenOf('73282932000009'));
    }

    public function testVatNumber(): void
    {
        $this->assertSame('FR44732829320', FrIdentityHelper::buildVatNumber('732829320'));
        $this->assertTrue(FrIdentityHelper::isValidVatNumber('FR 44 732829320'));
        $this->assertFalse(FrIdentityHelper::isValidVatNumber('FR45732829320'));
        $this->assertSame('732829320', FrIdentityHelper::sirenOf('FR44732829320'));
    }
}

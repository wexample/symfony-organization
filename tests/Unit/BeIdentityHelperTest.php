<?php

namespace Wexample\SymfonyOrganization\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wexample\SymfonyOrganization\Helper\BeIdentityHelper;

class BeIdentityHelperTest extends TestCase
{
    public function testEnterpriseNumber(): void
    {
        $this->assertTrue(BeIdentityHelper::isValidEnterpriseNumber('0123.456.749'));
        $this->assertFalse(BeIdentityHelper::isValidEnterpriseNumber('0123.456.748'));
        $this->assertFalse(BeIdentityHelper::isValidEnterpriseNumber('2123.456.749'));
        // Nine-digit numbers from before 2007.
        $this->assertTrue(BeIdentityHelper::isValidEnterpriseNumber('123456749'));
        $this->assertSame('0123.456.749', BeIdentityHelper::format('BE 0123456749'));
        $this->assertTrue(BeIdentityHelper::isValidVatNumber('BE 0123.456.749'));
        $this->assertSame('BE0123456749', BeIdentityHelper::toVatNumber('0123.456.749'));
    }
}

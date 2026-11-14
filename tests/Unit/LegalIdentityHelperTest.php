<?php

declare(strict_types=1);

namespace Wexample\SymfonyOrganization\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wexample\SymfonyOrganization\Entity\Organization;
use Wexample\SymfonyOrganization\Helper\LegalIdentityHelper;

class LegalIdentityHelperTest extends TestCase
{
    public function testSeparatorsAreDroppedAndLettersUppercased(): void
    {
        $this->assertSame('BE0123456749', LegalIdentityHelper::normalize(' be 0123.456.749 '));
        $this->assertSame('FR40303265045', LegalIdentityHelper::normalize("FR-40\t303 265 045"));
        $this->assertSame('HRB12345', LegalIdentityHelper::normalize('hrb 12345'));
        $this->assertSame('', LegalIdentityHelper::normalize(' . - '));
    }

    public function testAVatNumberIsKeptNormalized(): void
    {
        $this->assertSame('BE0123456749', (new Organization())->setVatNumber('be 0123.456.749')->getVatNumber());
        $this->assertNull((new Organization())->setVatNumber(null)->getVatNumber());
    }
}

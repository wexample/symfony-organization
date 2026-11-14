<?php

declare(strict_types=1);

namespace Wexample\SymfonyOrganization\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Stringable;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Component\HttpClient\Response\MockResponse;
use Wexample\SymfonyOrganization\Service\VatNumberVerifier;
use Wexample\SymfonyRemoteVies\Service\ViesClient;

/**
 * Confirmed only by a verdict of VIES: no verdict is no confirmation, and
 * says so in the logs.
 */
class VatNumberVerifierTest extends TestCase
{
    /**
     * @var list<array{level: string, message: string, context: array}>
     */
    private array $logs = [];

    public function testANumberViesValidatesIsConfirmed(): void
    {
        $this->assertTrue($this->verifier(new JsonMockResponse($this->verdict(true)))->isConfirmed('BE0123456749'));
        $this->assertSame([], $this->logs);
    }

    public function testANumberViesRefusesIsNot(): void
    {
        $this->assertFalse($this->verifier(new JsonMockResponse($this->verdict(false)))->isConfirmed('BE0123456749'));
        $this->assertSame([], $this->logs);
    }

    public function testNoVerdictConfirmsNothingAndIsLogged(): void
    {
        $unavailable = new JsonMockResponse(['errorWrappers' => [['error' => 'MS_UNAVAILABLE']]], ['http_code' => 500]);

        $this->assertFalse($this->verifier($unavailable)->isConfirmed('BE0123456749'));
        $this->assertCount(1, $this->logs);
        $this->assertSame('warning', $this->logs[0]['level']);
        $this->assertSame('BE0123456749', $this->logs[0]['context']['vat_number']);
        $this->assertSame('MS_UNAVAILABLE', $this->logs[0]['context']['error']);
    }

    public function testAnUnreadableAnswerConfirmsNothing(): void
    {
        $this->assertFalse($this->verifier(new MockResponse('<html>Bad gateway</html>', ['http_code' => 502]))->isConfirmed('BE0123456749'));
        $this->assertCount(1, $this->logs);
    }

    private function verifier(MockResponse $response): VatNumberVerifier
    {
        $logs = &$this->logs;
        $logger = new class($logs) extends AbstractLogger {
            public function __construct(private array &$logs)
            {
            }

            public function log($level, string|Stringable $message, array $context = []): void
            {
                $this->logs[] = ['level' => (string) $level, 'message' => (string) $message, 'context' => $context];
            }
        };

        return new VatNumberVerifier(new ViesClient(new MockHttpClient($response)), $logger);
    }

    private function verdict(bool $valid): array
    {
        return [
            'countryCode' => 'BE',
            'vatNumber' => '0123456749',
            'valid' => $valid,
            'requestDate' => '2026-11-14T10:00:00.000Z',
            'name' => $valid ? 'ACME SRL' : '---',
        ];
    }
}

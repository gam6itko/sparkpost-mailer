<?php declare(strict_types=1);

namespace Gam6itko\Symfony\Mailer\SparkPost\Test\Transport;

use Gam6itko\Symfony\Mailer\SparkPost\Mime\ABTestEmail;
use Gam6itko\Symfony\Mailer\SparkPost\Mime\SparkPostEmail;
use Gam6itko\Symfony\Mailer\SparkPost\Mime\TemplateEmail;
use Gam6itko\Symfony\Mailer\SparkPost\Transport\SparkPostApiTransport;
use Gam6itko\Symfony\Mailer\SparkPost\Transport\SparkPostSmtpTransport;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\DelayedEnvelope;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\HttpTransportException;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @coversDefaultClass \Gam6itko\Symfony\Mailer\SparkPost\Transport\SparkPostApiTransport
 */
class SparkPostSmtpTransportTest extends TestCase
{

    /**
     * @dataProvider dataToString
     */
    public function testToString(?string $region, ?string $host, ?int $port, string $endpoint)
    {
        $transport = new SparkPostSmtpTransport('username', 'password', $port, null, null, $region, $host);
        self::assertSame('smtp://' . $endpoint, (string) $transport);
    }

    public function dataToString(): iterable
    {
        yield [
            null, 'default', null, 'smtp.sparkpostmail.com:587',
        ];

        yield [
            'eu', 'default', null, 'smtp.eu.sparkpostmail.com:587',
        ];

        yield [
            'eu', 'example.com', null, 'example.com:587',
        ];

        yield [
            null, 'example.com', null, 'example.com:587',
        ];

        yield [
            null, 'default', 1111, 'smtp.sparkpostmail.com:1111',
        ];

        yield [
            'eu', 'default', 1111, 'smtp.eu.sparkpostmail.com:1111',
        ];

        yield [
            'eu', 'example.com', 1111, 'example.com:1111',
        ];

        yield [
            null, 'example.com', 1111, 'example.com:1111',
        ];
    }
}

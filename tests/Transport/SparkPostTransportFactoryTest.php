<?php declare(strict_types=1);

namespace Gam6itko\Symfony\Mailer\SparkPost\Test\Transport;

use Gam6itko\Symfony\Mailer\SparkPost\Transport\SparkPostApiTransport;
use Gam6itko\Symfony\Mailer\SparkPost\Transport\SparkPostSmtpTransport;
use Gam6itko\Symfony\Mailer\SparkPost\Transport\SparkPostTransportFactory;
use Symfony\Component\Mailer\Test\TransportFactoryTestCase;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\TransportFactoryInterface;

/**
 * @coversDefaultClass \Gam6itko\Symfony\Mailer\SparkPost\Transport\SparkPostTransportFactory
 */
class SparkPostTransportFactoryTest extends TransportFactoryTestCase
{
    public function getFactory(): TransportFactoryInterface
    {
        return new SparkPostTransportFactory($this->getDispatcher(), $this->getClient(), $this->getLogger());
    }

    public function supportsProvider(): iterable
    {
        yield [
            new Dsn('sparkpost', 'default'),
            true,
        ];

        yield [
            new Dsn('sparkpost+api', 'default'),
            true,
        ];

        yield [
            new Dsn('sparkpost+https', 'default'),
            false,
        ];

        yield [
            new Dsn('sparkpost+smtp', 'default'),
            true,
        ];

        yield [
            new Dsn('sparkpost+smtps', 'default'),
            true,
        ];

        yield [
            new Dsn('sparkpost+smtp', 'example.com'),
            true,
        ];
    }

    public function createProvider(): iterable
    {
        $client = $this->getClient();
        $dispatcher = $this->getDispatcher();
        $logger = $this->getLogger();

        yield [
            new Dsn('sparkpost+api', 'default', self::USER),
            new SparkPostApiTransport(self::USER, $client, $dispatcher, $logger),
        ];

        yield [
            new Dsn('sparkpost', 'default', self::USER, self::PASSWORD),
            new SparkPostSmtpTransport(self::USER, self::PASSWORD, null, $dispatcher, $logger),
        ];

        yield [
            new Dsn('sparkpost+smtp', 'default', self::USER, self::PASSWORD, 587),
            new SparkPostSmtpTransport(self::USER, self::PASSWORD, 587, $dispatcher, $logger),
        ];

        yield [
            new Dsn('sparkpost+smtps', 'default', self::USER, self::PASSWORD, 2525),
            new SparkPostSmtpTransport(self::USER, self::PASSWORD, 2525, $dispatcher, $logger),
        ];
    }

    public function unsupportedSchemeProvider(): iterable
    {
        yield [
            new Dsn('sparkpost+http', 'default', self::USER),
            'The "sparkpost+http" scheme is not supported; supported schemes for mailer "sparkpost" are: "sparkpost", "sparkpost+api", "sparkpost+smtp", "sparkpost+smtps".',
        ];
    }

    public function incompleteDsnProvider(): iterable
    {
        yield [new Dsn('sparkpost+api', 'default')];

        yield [new Dsn('sparkpost+smtp', 'default')];

        yield [new Dsn('sparkpost+smtp', 'default', self::USER)];
    }
}

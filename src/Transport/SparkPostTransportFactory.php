<?php declare(strict_types=1);

namespace Gam6itko\Symfony\Mailer\SparkPost\Transport;

use Symfony\Component\Mailer\Exception\UnsupportedSchemeException;
use Symfony\Component\Mailer\Transport\AbstractTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\TransportInterface;

class SparkPostTransportFactory extends AbstractTransportFactory
{
    public function create(Dsn $dsn): TransportInterface
    {
        $scheme = $dsn->getScheme();
        $user = $this->getUser($dsn);
        $port = $dsn->getPort();
        $region = $dsn->getOption('region');

        switch (true) {
            case 'sparkpost+api' === $scheme:
                return new SparkPostApiTransport($user, $this->client, $this->dispatcher, $this->logger, $region, $dsn->getHost());

            case 'sparkpost' === $scheme:
            case 'sparkpost+smtp' === $scheme:
            case 'sparkpost+smtps' === $scheme:
                $password = $this->getPassword($dsn);
                return new SparkPostSmtpTransport($user, $password, $port, $this->dispatcher, $this->logger, $region, $dsn->getHost());
        }

        throw new UnsupportedSchemeException($dsn, 'sparkpost', $this->getSupportedSchemes());
    }

    protected function getSupportedSchemes(): array
    {
        return ['sparkpost', 'sparkpost+api', 'sparkpost+smtp', 'sparkpost+smtps'];
    }
}

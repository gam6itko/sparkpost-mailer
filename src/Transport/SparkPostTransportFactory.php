<?php

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

        if ('sparkpost+api' === $scheme) {
            return new SparkPostApiTransport($user, $this->client, $this->dispatcher, $this->logger);
        }

        if (in_array($scheme, ['sparkpost', 'sparkpost+smtp', 'sparkpost+smtps'])) {
            $password = $this->getPassword($dsn);

            return new SparkPostSmtpTransport($user, $password, $port, $this->dispatcher, $this->logger);
        }

        throw new UnsupportedSchemeException($dsn, 'sparkpost', $this->getSupportedSchemes());
    }

    protected function getSupportedSchemes(): array
    {
        return ['sparkpost', 'sparkpost+api', 'sparkpost+smtp', 'sparkpost+smtps'];
    }
}

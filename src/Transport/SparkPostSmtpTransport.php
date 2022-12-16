<?php declare(strict_types=1);

namespace Gam6itko\Symfony\Mailer\SparkPost\Transport;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SparkPostSmtpTransport extends EsmtpTransport
{
    public function __construct(string $username, string $password, int $port = null, EventDispatcherInterface $dispatcher = null, LoggerInterface $logger = null, ?string $region = null, string $host = 'default')
    {
        if ('default' === $host) {
            $host = \sprintf('smtp%s.sparkpostmail.com', $region ? '.'.$region : '');
        }
        parent::__construct($host, $port ?: 587, false, $dispatcher, $logger);

        $this->setUsername($username);
        $this->setPassword($password);
    }
}

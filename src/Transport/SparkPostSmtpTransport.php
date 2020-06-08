<?php declare(strict_types=1);

namespace Gam6itko\Symfony\Mailer\SparkPost\Transport;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SparkPostSmtpTransport extends EsmtpTransport
{
    public function __construct(string $username, string $password, int $port = null, EventDispatcherInterface $dispatcher = null, LoggerInterface $logger = null)
    {
        parent::__construct('smtp.sparkpostmail.com', $port ?: 587, false, $dispatcher, $logger);

        $this->setUsername($username);
        $this->setPassword($password);
    }
}

<?php declare(strict_types=1);

namespace Gam6itko\Symfony\Mailer\SparkPost\Test\EventListener;

use DG\BypassFinals;
use Gam6itko\Symfony\Mailer\SparkPost\EventListener\SinkEnvelopeListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\RawMessage;

/**
 * @coversDefaultClass \Gam6itko\Symfony\Mailer\SparkPost\EventListener\SinkEnvelopeListener
 */
class SinkEnvelopeListenerTest extends TestCase
{
    public function testSinkEmail()
    {
        $envelope = new Envelope(Address::fromString('test case <nobody@nowhere.net>'), [
            new Address('gam6itko@gmail.com'),
            new Address('fabien@symfony.com'),
        ]);

        $message = $this->createMock(RawMessage::class);
        $messageEvent = new MessageEvent($message, $envelope, 'sparkpost');

        $listener = new SinkEnvelopeListener();
        $listener->onMessage($messageEvent);
        self::assertEquals([
            'gam6itko@gmail.com.sink.sparkpostmail.com',
            'fabien@symfony.com.sink.sparkpostmail.com',
        ], array_map(static function (Address $address): string {
            return $address->getAddress();
        }, $envelope->getRecipients()));
    }

    public function testNoPrefix()
    {
        BypassFinals::enable();

        $messageEvent = $this->createMock(MessageEvent::class);
        $messageEvent
            ->expects(self::never())
            ->method('getEnvelope');

        $listener = new SinkEnvelopeListener('');
        $listener->onMessage($messageEvent);
    }
}

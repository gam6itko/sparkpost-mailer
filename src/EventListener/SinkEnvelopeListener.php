<?php

namespace Gam6itko\Symfony\Mailer\SparkPost\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
class SinkEnvelopeListener implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $sinkSuffix;

    public function __construct(string $sinkSuffix = '.sink.sparkpostmail.com')
    {
        $this->sinkSuffix = $sinkSuffix;
    }

    public function onMessage(MessageEvent $event): void
    {
        if (!$this->sinkSuffix) {
            return;
        }

        $event->getEnvelope()->setRecipients(array_map(function (Address $address): Address {
            return new Address($address->getAddress().$this->sinkSuffix, $address->getName());
        }, $event->getEnvelope()->getRecipients()));
    }

    public static function getSubscribedEvents()
    {
        return [
            // should be the last one to allow header changes by other listeners first
            MessageEvent::class => 'onMessage',
        ];
    }
}

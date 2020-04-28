<?php

namespace Gam6itko\Symfony\Mailer\SparkPost\Transport;

use Gam6itko\Symfony\Mailer\SparkPost\Mime\SparkPostEmail;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractApiTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Header\ParameterizedHeader;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SparkPostApiTransport extends AbstractApiTransport
{
    /**
     * @var string
     */
    private $key;

    public function __construct(string $key, HttpClientInterface $client = null, EventDispatcherInterface $dispatcher = null, LoggerInterface $logger = null)
    {
        $this->key = $key;

        parent::__construct($client, $dispatcher, $logger);
    }

    public function __toString(): string
    {
        return sprintf('sparkpost+api://%s', $this->getEndpoint());
    }

    protected function doSendApi(SentMessage $sentMessage, Email $email, Envelope $envelope): ResponseInterface
    {
        $payload = [
            'recipients' => $this->buildRecipients($envelope),
            'content'    => $this->buildContent($email, $envelope),
        ];

        if ($email instanceof SparkPostEmail) {
            $payload = array_merge($payload, array_filter([
                'campaign_id'       => $email->getCampaignId(),
                'description'       => $email->getDescription(),
                'options'           => $email->getOptions(),
                'metadata'          => $email->getMetadata(),
                'substitution_data' => $email->getSubstitutionData(),
            ]));
        }

        return $this->client->request('POST', 'https://api.sparkpost.com/api/v1/transmissions/', [
            'headers' => [
                'Authorization' => $this->key,
                'Content-Type'  => 'application/json',
            ],
            'json'    => $payload,
        ]);
    }

    private function getEndpoint(): ?string
    {
        return $this->host.($this->port ? ':'.$this->port : '');
    }

    private function buildRecipients(Envelope $envelope): array
    {
        $result = [];
        foreach ($envelope->getRecipients() as $to) {
            $result[] = [
                'address' => array_filter([
                    'name'  => $to->getName(),
                    'email' => $to->getAddress(),
                ]),
            ];
        }

        return $result;
    }

    private function buildContent(Email $email, Envelope $envelope): array
    {
        if ($email instanceof SparkPostEmail && $email->getContent()) {
            return $email->getContent();
        }

        $from = $envelope->getSender();

        return array_filter([
            'from'        => array_filter([
                'name'  => $from->getName(),
                'email' => $from->getAddress(),
            ]),
            'subject'     => $email->getSubject(),
            'text'        => $email->getTextBody(),
            'html'        => $email->getHtmlBody(),
            'replyTo'     => $email->getReplyTo(),
            'attachments' => $this->buildAttachments($email),
        ]);
    }

    private function buildAttachments(Email $email): array
    {
        $result = [];
        foreach ($email->getAttachments() as $attachment) {
            /** @var ParameterizedHeader $file */
            $file = $attachment->getPreparedHeaders()->get('Content-Disposition');
            /** @var ParameterizedHeader $type */
            $type = $attachment->getPreparedHeaders()->get('Content-Type');

            $result[] = [
                'name' => $file->getParameter('filename'),
                'type' => $type->getValue(),
                'data' => base64_encode($attachment->getBody()),
            ];
        }

        return $result;
    }
}

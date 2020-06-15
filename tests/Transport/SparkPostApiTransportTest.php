<?php declare(strict_types=1);

namespace Gam6itko\Symfony\Mailer\SparkPost\Test\Transport;

use Gam6itko\Symfony\Mailer\SparkPost\Mime\ABTestEmail;
use Gam6itko\Symfony\Mailer\SparkPost\Mime\SparkPostEmail;
use Gam6itko\Symfony\Mailer\SparkPost\Mime\TemplateEmail;
use Gam6itko\Symfony\Mailer\SparkPost\Transport\SparkPostApiTransport;
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
class SparkPostApiTransportTest extends TestCase
{
    private function createDefaultMessage(): Email
    {
        return (new Email())
            ->from('sender@mail.com')
            ->to('recipient@mail.com')
            ->subject('Test email')
            ->text('Test email for you!')
            ->attach(str_pad('', 200, 'A'), 'name.txt');
    }

    private function createDefaultEnvelope(): Envelope
    {
        return new Envelope(new Address('gam6itko@gmail.com'), [new Address('fabien@symfony.com')]);
    }

    private function createDefaultResponse(): ResponseInterface
    {
        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects(self::atLeastOnce())
            ->method('getStatusCode')
            ->willReturn(200);
        return $response;
    }

    /**
     * @dataProvider dataSend
     */
    public function testSend(RawMessage $message, Envelope $envelope, string $expectedJson): void
    {
        $response = $this->createDefaultResponse();

        $client = $this->createMock(HttpClientInterface::class);
        $client
            ->expects(self::once())
            ->method('request')
            ->willReturnCallback(function (string $method, string $url, array $options = []) use ($expectedJson, $response) {
                self::assertEquals('POST', $method);
                self::assertEquals('https://api.sparkpost.com/api/v1/transmissions/', $url);
                self::assertArrayHasKey('headers', $options);
                self::assertEquals([
                    'Authorization' => 'api-key',
                    'Content-Type'  => 'application/json',
                ], $options['headers']);
                self::assertIsArray($options['json']);
                self::assertJsonStringEqualsJsonString($expectedJson, json_encode($options['json']));

                return $response;
            });
        $transport = new SparkPostApiTransport('api-key', $client);
        $transport->send($message, $envelope);
    }

    public function dataSend()
    {
        $json = <<<JSON
{
    "recipients": [
        {
            "address": {
                "email": "fabien@symfony.com"
            }
        }
    ],
    "content": {
        "from": {
            "email": "gam6itko@gmail.com"
        },
        "subject": "Test email",
        "text": "Test email for you!"
    }
}
JSON;
        yield [
            (new Email())
                ->from('sender@mail.com')
                ->to('recipient@mail.com')
                ->subject('Test email')
                ->text('Test email for you!'),
            new Envelope(new Address('gam6itko@gmail.com'), [new Address('fabien@symfony.com')]),
            $json,
        ];

        $json = <<<JSON
{
  "options": {
    "click_tracking": false,
    "transactional": true,
    "ip_pool": "my_ip_pool",
    "inline_css": true
  },
  "description": "Christmas Campaign Email",
  "campaign_id": "christmas_campaign",
  "metadata": {
    "user_type": "students",
    "education_level": "college"
  },
  "substitution_data": {
    "sender": "Big Store Team",
    "holiday_name": "Christmas"
  },
  "recipients": [
    {
      "address": {
        "email": "recipient@mail.com"
      }
    },
    {
      "address": {
        "email": "recipient2@mail.com"
      }
    }
  ],
  "content": {
    "from": {
      "email": "sender@mail.com"
    },
    "subject": "Test email",
    "text": "Test email for you!"
  }
}
JSON;
        $email = new SparkPostEmail();
        $email
            ->from('sender@mail.com')
            ->to('recipient@mail.com')
            ->addTo('recipient2@mail.com')
            ->subject('Test email')
            ->text('Test email for you!')
            ->setCampaignId('christmas_campaign')
            ->setDescription('Christmas Campaign Email')
            ->setOptions([
                'click_tracking' => false,
                'transactional'  => true,
                'ip_pool'        => 'my_ip_pool',
                'inline_css'     => true,
            ])
            ->setMetadata([
                'user_type'       => 'students',
                'education_level' => 'college',
            ])
            ->setSubstitutionData([
                'sender'       => 'Big Store Team',
                'holiday_name' => 'Christmas',
            ]);
        yield [
            $email,
            new DelayedEnvelope($email),
            $json,
        ];

        $json = <<<JSON
{
  "content": {
    "template_id": "black_friday",
    "use_draft_template": true
  },
  "substitution_data": {
    "discount": "25%"
  },
  "recipients": [
    {
      "address": {
        "email": "wilma@flintstone.com",
        "name": "Wilma Flintstone"
      }
    }
  ]
}
JSON;
        $email = (new TemplateEmail('black_friday', true))
            ->to(Address::fromString('Wilma Flintstone <wilma@flintstone.com>'))
            ->setSubstitutionData([
                'discount' => '25%',
            ]);
        yield [
            $email,
            new DelayedEnvelope($email),
            $json,
        ];

        $json = <<<JSON
{
  "content": {
    "ab_test_id": "password_reset"
  },
  "recipients": [
    {
      "address": {
        "email": "wilma@flintstone.com",
        "name": "Wilma Flintstone"
      }
    }
  ]
}
JSON;
        $email = (new ABTestEmail('password_reset'))
            ->to(Address::fromString('Wilma Flintstone <wilma@flintstone.com>'));
        yield [
            $email,
            new DelayedEnvelope($email),
            $json,
        ];
    }

    public function testTruncateAttachmentData(): void
    {
        $response = $this->createDefaultResponse();

        $client = $this->createMock(HttpClientInterface::class);
        $client
            ->expects(self::once())
            ->method('request')
            ->willReturn($response);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::once())
            ->method('debug')
            ->willReturnCallback(static function (string $message, array $context) {
                self::assertTrue(isset($context['content']['attachments']));
                self::assertCount(1, $context['content']['attachments']);
                self::assertSame('<<<truncated>>>', $context['content']['attachments'][0]['data']);
            });
        $transport = new SparkPostApiTransport('api-key', $client, null, $logger);
        $transport->send($this->createDefaultMessage(), $this->createDefaultEnvelope());
    }

    public function testToString()
    {
        $client = $this->createMock(HttpClientInterface::class);
        $transport = new SparkPostApiTransport('api-key', $client);
        self::assertSame('sparkpost+api://', (string) $transport);
    }

    public function testNotSuccess(): void
    {
        self::expectException(HttpTransportException::class);
        self::expectExceptionMessage('[1902] Message generation rejected. Blocked Sending Domain <domain.com>.');

        $error = [
            'errors'  => [
                [
                    'message'     => 'Message generation rejected',
                    'description' => 'Blocked Sending Domain <domain.com>',
                    'code'        => '1902',
                ],
            ],
            'results' => [
                'total_rejected_recipients' => 0,
                'total_accepted_recipients' => 1,
                'id'                        => '012345678901234567',
            ],
        ];
        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects(self::atLeastOnce())
            ->method('getStatusCode')
            ->willReturn(400);
        $response
            ->expects(self::atLeastOnce())
            ->method('getContent')
            ->willReturn(json_encode($error));

        $client = $this->createMock(HttpClientInterface::class);
        $client
            ->expects(self::once())
            ->method('request')
            ->willReturn($response);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::once())
            ->method('error');

        $transport = new SparkPostApiTransport('api-key', $client, null, $logger);
        $transport->send($this->createDefaultMessage(), $this->createDefaultEnvelope());
    }
}

<?php declare(strict_types=1);

namespace Gam6itko\Symfony\Mailer\SparkPost\Test\Mime;

use Gam6itko\Symfony\Mailer\SparkPost\Mime\SparkPostEmail;
use PHPUnit\Framework\TestCase;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 *
 * @coversDefaultClass \Gam6itko\Symfony\Mailer\SparkPost\Mime\SparkPostEmail
 */
class SparkPostEmailTest extends TestCase
{
    public function testSerialize(): void
    {
        $srcEmail = new SparkPostEmail();
        $srcEmail
            ->setCampaignId('2021')
            ->setDescription('testemail')
            ->setOptions(['option1' => true])
            ->setContent(['data' => ['foo' => 'bar']])
            ->addSubstitutionData('name', 'Alexander')
            ->addSubstitutionData('nickname', 'gam6tiko')
            ->addMetadata('gender', 'male');

        $destEmail = new SparkPostEmail();
        $destEmail->unserialize($srcEmail->serialize());
        self::assertSame($srcEmail->getCampaignId(), $destEmail->getCampaignId());
        self::assertSame($srcEmail->getDescription(), $destEmail->getDescription());
        self::assertSame($srcEmail->getOptions(), $destEmail->getOptions());
        self::assertSame($srcEmail->getContent(), $destEmail->getContent());
        self::assertSame($srcEmail->getSubstitutionData(), $destEmail->getSubstitutionData());
        self::assertSame($srcEmail->getMetadata(), $destEmail->getMetadata());
    }
}

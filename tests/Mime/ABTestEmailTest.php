<?php declare(strict_types=1);

namespace Gam6itko\Symfony\Mailer\SparkPost\Test\Mime;

use Gam6itko\Symfony\Mailer\SparkPost\Mime\ABTestEmail;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Gam6itko\Symfony\Mailer\SparkPost\Mime\ABTestEmail
 */
class ABTestEmailTest extends TestCase
{
    public function test()
    {
        $email = new ABTestEmail('id');
        self::assertEquals(
            [
                'ab_test_id' => 'id',
            ],
            $email->getContent()
        );
        self::assertEmpty($email->getSubstitutionData());
    }
}

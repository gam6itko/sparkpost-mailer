<?php declare(strict_types=1);

namespace Gam6itko\Symfony\Mailer\SparkPost\Test\Mime;

use Gam6itko\Symfony\Mailer\SparkPost\Mime\TemplateEmail;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Gam6itko\Symfony\Mailer\SparkPost\Mime\TemplateEmail
 */
class TemplateEmailTest extends TestCase
{
    public function test()
    {
        $email = new TemplateEmail('template', true);
        self::assertEquals(
            [
                'template_id'        => 'template',
                'use_draft_template' => true,
            ],
            $email->getContent()
        );
    }
}

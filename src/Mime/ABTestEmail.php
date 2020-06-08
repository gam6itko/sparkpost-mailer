<?php declare(strict_types=1);

namespace Gam6itko\Symfony\Mailer\SparkPost\Mime;

class ABTestEmail extends SparkPostEmail
{
    public function __construct(string $abTestId)
    {
        $this->setContent([
            'ab_test_id' => $abTestId,
        ]);

        parent::__construct();
    }

    public function generateMessageId(): string
    {
        return bin2hex(random_bytes(16)).'@abtest-sparkpost.com';
    }
}

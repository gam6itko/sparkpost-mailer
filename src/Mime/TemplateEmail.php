<?php

namespace Gam6itko\Symfony\Mailer\SparkPost\Mime;

use Symfony\Component\Mime\Exception\LogicException;

class TemplateEmail extends SparkPostEmail
{
    public function __construct(string $templateId, bool $useDraftTemplate = false)
    {
        $this->setContent([
            'template_id'        => $templateId,
            'use_draft_template' => $useDraftTemplate,
        ]);

        parent::__construct();
    }

    public function generateMessageId(): string
    {
        return bin2hex(random_bytes(16)).'@template-sparkpost.com';
    }
}

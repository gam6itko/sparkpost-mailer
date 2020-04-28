<?php

namespace Gam6itko\Symfony\Mailer\SparkPost\Mime;

trait HasMetadataTrait
{
    /**
     * @var array
     */
    private $metadata = [];

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @return HasMetadataTrait
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function addMetadata(string $key, $value)
    {
        $this->metadata[$key] = $value;

        return $this;
    }
}

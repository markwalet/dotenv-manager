<?php

namespace MarkWalet\DotenvManager\Changes\Concerns;

trait HasKey
{
    /**
     * @var string
     */
    protected $key;

    /**
     * Get the key of the change.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}
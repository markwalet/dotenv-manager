<?php

namespace MarkWalet\DotenvManager\Adapters;

class FakeDotenvAdapter implements DotenvAdapter
{
    /**
     * @var string
     */
    private $source;

    /**
     * Get the contents of the environment file.
     *
     * @return string
     */
    public function read(): string
    {
        return $this->source;
    }

    /**
     * Write a string to the environment file.
     *
     * @param string $content
     *
     * @return bool
     */
    public function write(string $content): bool
    {
        $this->source = $content;

        return true;
    }

    /**
     * Write a string to the environment file.
     *
     * @param string $content
     */
    public function setSource(string $content)
    {
        $this->source = $content;
    }
}
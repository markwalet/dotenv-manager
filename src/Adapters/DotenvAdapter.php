<?php

namespace MarkWalet\DotenvManager\Adapters;

interface DotenvAdapter
{
    /**
     * Get the contents of the environment file.
     *
     * @return string
     */
    public function read(): string;

    /**
     * Write a string to the environment file.
     *
     * @param string $content
     *
     * @return bool
     */
    public function write(string $content): bool;
}
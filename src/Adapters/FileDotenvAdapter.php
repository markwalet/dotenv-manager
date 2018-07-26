<?php

namespace MarkWalet\DotenvManager\Adapters;

class FileDotenvAdapter implements DotenvAdapter
{
    /**
     * @var string
     */
    private $path;

    /**
     * FileDotenvAdapter constructor.
     *
     * @param string $path
     */
    function __construct(string $path)
    {
        $this->path = $path;

        // TODO: Throw FileNotFoundException when path doesn't contain a accessible file.
    }

    /**
     * Get the contents of the environment file.
     *
     * @return string
     */
    public function read(): string
    {
        return file_get_contents($this->path);
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
        return file_put_contents($this->path, $content) > 0;
    }
}
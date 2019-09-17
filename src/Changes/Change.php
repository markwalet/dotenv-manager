<?php

namespace MarkWalet\DotenvManager\Changes;

abstract class Change
{
    /**
     * Apply the pending change to the given content.
     *
     * @param $content
     *
     * @return mixed
     */
    abstract public function apply(string $content): string;
}

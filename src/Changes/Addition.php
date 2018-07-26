<?php

namespace MarkWalet\DotenvManager\Changes;

use MarkWalet\DotenvManager\Changes\Concerns\HasKey;
use MarkWalet\DotenvManager\Changes\Concerns\HasValue;

class Addition extends Change
{
    use HasValue, HasKey;

    /**
     * @var string
     */
    private $after;

    /**
     * @var string
     */
    private $before;

    /**
     * Addition constructor.
     *
     * @param string $key
     * @param $value
     */
    function __construct(string $key, $value = null)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Apply the pending change to the given content.
     *
     * @param $content
     *
     * @return string
     */
    public function apply(string $content): string
    {
        $position = $this->after ?? $this->before;
        $search = (is_null($position))
            ? '/(\z)/'
            : '/('.$position.'=.*)/';

        // TODO: Check if this
        $replacement = is_null($this->before)
            ? '$1'.PHP_EOL.$this->getKey().'='.$this->getValue()
            : $this->getKey().'='.$this->getValue().PHP_EOL.'$1';

        return preg_replace($search, $replacement, $content);
    }

    /**
     * Place new value after a key.
     *
     * @param string $key
     *
     * @return Change
     */
    public function after(string $key): Change
    {
        $this->before = null;
        $this->after = $key;

        return $this;
    }

    /**
     * Place new value before a key.
     *
     * @param string $key
     *
     * @return Change
     */
    public function before(string $key): Change
    {
        $this->after = null;
        $this->before = $key;

        return $this;
    }
}
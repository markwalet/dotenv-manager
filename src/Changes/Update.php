<?php

namespace MarkWalet\DotenvManager\Changes;

use MarkWalet\DotenvManager\Changes\Concerns\HasKey;
use MarkWalet\DotenvManager\Changes\Concerns\HasValue;

class Update extends Change
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
     * Update constructor.
     *
     * @param string $key
     * @param mixed $value
     */
    public function __construct(string $key, $value = null)
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
        $search = '/(?:'.$this->getKey().'=.*)/';
        $replacement = $this->getKey().'='.$this->getValue();

        $content = preg_replace($search, $replacement, $content);

        if ($this->before) {
            $content = (new Move($this->getKey()))->before($this->before)->apply($content);
        }

        if ($this->after) {
            $content = (new Move($this->getKey()))->after($this->after)->apply($content);
        }

        return $content;
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

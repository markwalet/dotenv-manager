<?php

namespace MarkWalet\DotenvManager\Changes\Concerns;

trait HasValue
{
    /**
     * @var string
     */
    private $value;

    /**
     * Set a new value.
     *
     * @param $value
     *
     * @return $this
     */
    public function value($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the new value of the change.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->transform($this->value);
    }

    /**
     * Transform the given value to a string.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function transform($value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_null($value)) {
            return 'null';
        }

        if (preg_match('/\s/', $value)) {
            return (substr($value, 0, 1) === '"' && substr($value, -1) === '"')
                ? $value
                : "\"$value\"";
        }

        return $value;
    }
}

<?php

namespace MarkWalet\DotenvManager;

use MarkWalet\DotenvManager\Changes\Addition;
use MarkWalet\DotenvManager\Changes\Change;
use MarkWalet\DotenvManager\Changes\Delete;
use MarkWalet\DotenvManager\Changes\Move;
use MarkWalet\DotenvManager\Changes\Update;
use MarkWalet\DotenvManager\Exceptions\InvalidArgumentException;
use MarkWalet\DotenvManager\Exceptions\MethodNotFoundException;

/**
 * Class DotenvBuilder.
 *
 * @method Addition add(string $key, $value = null)
 * @method Addition create(string $key, $value = null)
 * @method Update set(string $key, $value = null)
 * @method Update update(string $key, $value = null)
 * @method bool move(string $key)
 * @method Delete delete(string $key)
 * @method Delete unset(string $key)
 */
class DotenvBuilder
{
    /**
     * @var array
     */
    private $methods = [
        'add' => Addition::class,
        'create' => Addition::class,
        'set' => Update::class,
        'update' => Update::class,
        'move' => Move::class,
        'delete' => Delete::class,
        'unset' => Delete::class,
    ];

    /**
     * A list of pending changes.
     *
     * @var Change[]
     */
    private $changes = [];

    /**
     * Add a pending change.
     *
     * @param Change $change
     * @return Change
     */
    public function change(Change $change): Change
    {
        return $this->changes[] = $change;
    }

    /**
     * Apply changes to the given content.
     *
     * @param string $content
     * @return string
     */
    public function apply(string $content): string
    {
        /** @var Change $change */
        foreach ($this->changes as $change) {
            $content = $change->apply($content);
        }

        return $content;
    }

    /**
     * Extend the builder with a new method.
     *
     * @param string $method Name of the method
     * @param string $class Class name that implements PendingChange
     * @throws InvalidArgumentException
     */
    public function extend(string $method, string $class)
    {
        if (class_exists($class) === false) {
            throw new InvalidArgumentException("Class {$class} is not found.");
        }
        if (is_subclass_of($class, Change::class) === false) {
            throw new InvalidArgumentException("{$class} does not extend ".Change::class);
        }

        $this->methods[$method] = $class;
    }

    /**
     * Get a list of installed methods.
     *
     * @return array
     */
    public function methods(): array
    {
        return $this->methods;
    }

    /**
     * Call a change action dynamically.
     *
     * @param string $method
     * @param array $parameters
     * @return Change
     * @throws MethodNotFoundException
     */
    public function __call($method, $parameters)
    {
        // Check if requested method is registered as action.
        if (array_key_exists($method, $this->methods) === false) {
            throw new MethodNotFoundException();
        }

        // Instantiate new change object.
        $change = new $this->methods[$method](...$parameters);

        // Add change to builder.
        return $this->change($change);
    }
}

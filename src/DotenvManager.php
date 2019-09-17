<?php

namespace MarkWalet\DotenvManager;

use Closure;
use MarkWalet\DotenvManager\Adapters\DotenvAdapter;
use MarkWalet\DotenvManager\Exceptions\InvalidArgumentException;

/**
 * Class DotenvManager.
 *
 * @method bool add(string $key, $value = null)
 * @method bool create(string $key, $value = null)
 * @method bool set(string $key, $value = null)
 * @method bool update(string $key, $value = null)
 * @method bool move(string $key)
 * @method bool delete(string $key)
 * @method bool unset(string $key)
 */
class DotenvManager
{
    /**
     * @var DotenvAdapter
     */
    private $adapter;

    /**
     * @var DotenvBuilder
     */
    private $builder;

    /**
     * DotenvManager constructor.
     *
     * @param DotenvAdapter $adapter
     */
    public function __construct(DotenvAdapter $adapter)
    {
        $this->adapter = $adapter;
        $this->builder = new DotenvBuilder;
    }

    /**
     * Update and persist the dotenv file.
     *
     * @param Closure $callback
     *
     * @return bool
     */
    public function mutate(Closure $callback)
    {
        $callback($this->builder());

        return $this->persist();
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
        $this->builder()->extend($method, $class);
    }

    /**
     * Persist pending changes to the dotenv file.
     *
     * @return bool
     */
    private function persist()
    {
        $content = $this->adapter->read();

        $content = $this->builder->apply($content);

        return $this->adapter->write($content);
    }

    /**
     * Get the builder instance.
     *
     * @return DotenvBuilder
     */
    public function builder()
    {
        return $this->builder;
    }

    /**
     * Call a change action dynamically.
     *
     * @param string $method
     * @param array $parameters
     *
     * @return bool
     */
    public function __call($method, $parameters)
    {
        // Call method on builder.
        $this->builder->$method(...$parameters);

        // Persist changes.
        return $this->persist();
    }
}

<?php

declare(strict_types=1);

namespace Rinimisini\PhpUtilities\Iterator;

use Generator;
use Iterator;
use WeakMap;

/**
 * Custom iterator for the WeakSet, optimized for large datasets.
 * It iterates over WeakMap without holding unnecessary references.
 */
class WeakSetIterator implements Iterator
{
    /**
     * @var WeakMap<object, bool> $weakMap The WeakMap being iterated over.
     */
    private WeakMap $weakMap;

    /**
     * @var array $keys An array of object keys.
     */
    private array $keys;

    /**
     * @var int $position The current position in the iterator.
     */
    private int $position = 0;

    /**
     * Constructs a WeakSetIterator.
     *
     * @param WeakMap<object, bool> $weakMap The WeakMap to iterate over.
     */
    public function __construct(WeakMap $weakMap)
    {
        $this->weakMap = $weakMap;
        $this->keys = array_keys(iterator_to_array($weakMap));
    }

    /**
     * Returns the current object in the set.
     *
     * @return object The current object.
     */
    public function current(): object
    {
        return $this->keys[$this->position];
    }

    /**
     * Moves to the next object in the set.
     *
     * @return void
     */
    public function next(): void
    {
        $this->position++;
    }

    /**
     * Returns the key (position) of the current object.
     *
     * @return int The current position.
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * Checks if the current position is valid.
     *
     * @return bool True if the position is valid, false otherwise.
     */
    public function valid(): bool
    {
        return isset($this->keys[$this->position]);
    }

    /**
     * Resets the iterator to the first object.
     *
     * @return void
     */
    public function rewind(): void
    {
        $this->position = 0;
        $this->keys = array_keys(iterator_to_array($this->weakMap));
    }

    /**
     * Generator function to iterate over the items.
     *
     * @return Generator
     */
    public function getGenerator(): Generator
    {
        foreach ($this->weakMap as $key => $item) {
            yield $key => $item;
        }
    }
}
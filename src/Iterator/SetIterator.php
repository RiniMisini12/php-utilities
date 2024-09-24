<?php

namespace Rinimisini\PhpUtilities\Iterator;

use Generator;
use Iterator;

/**
 * Custom iterator for the Set class, optimized for large datasets and memory efficiency.
 */
class SetIterator implements Iterator
{
    /**
     * The array of items to iterate over.
     * @var array
     */
    private array $items;

    /**
     * The current position in the iteration.
     * @var int
     */
    private int $position = 0;

    /**
     * Constructs a new SetIterator.
     *
     * @param array $items The array of items to iterate over.
     */
    public function __construct(array $items)
    {
        $this->items = $items;
        $this->position = 0;
    }

    /**
     * Return the current element without copying all elements.
     *
     * @return mixed
     */
    public function current(): mixed
    {
        return current($this->items);
    }

    /**
     * Move forward to the next element without needing to manually increment position.
     *
     * @return void
     */
    public function next(): void
    {
        next($this->items);
        ++$this->position;
    }

    /**
     * Return the key of the current element.
     *
     * @return mixed The key of the current element.
     */
    public function key(): mixed
    {
        return key($this->items);
    }

    /**
     * Check if there is a valid element at the current position.
     *
     * @return bool
     */
    public function valid(): bool
    {
        return key($this->items) !== null;
    }

    /**
     * Rewind the Iterator to the first element.
     *
     * @return void
     */
    public function rewind(): void
    {
        reset($this->items);
        $this->position = 0;
    }

    /**
     * Generator function to iterate over the items.
     *
     * @return Generator
     */
    public function getGenerator(): Generator
    {
        foreach ($this->items as $key => $item) {
            yield $key => $item;
        }
    }
}
<?php

declare(strict_types=1);

namespace Rinimisini\PhpUtilities;

use Countable;
use IteratorAggregate;
use ArrayIterator;

/**
 * A PHP Set implementation similar to JavaScript's Set.
 * It stores unique elements and provides methods to perform set operations such as union, intersection, and difference.
 * Elements in the set are guaranteed to be unique and can be of any type.
 */
class Set implements Countable, IteratorAggregate
{
    /**
     * @var array $items The underlying storage for the set elements.
     */
    private array $items = [];

    /**
     * Adds a new element to the set. If the element already exists, it will not be added again.
     *
     * @param mixed $value The value to be added to the set.
     * @return self Returns the set instance for method chaining.
     */
    public function add(mixed $value): self
    {
        $key = $this->getKey($value);
        $this->items[$key] = $value;
        return $this;
    }

    /**
     * Deletes an element from the set if it exists.
     *
     * @param mixed $value The value to be removed from the set.
     * @return bool Returns true if the value was successfully deleted, false if the value was not found.
     */
    public function delete(mixed $value): bool
    {
        $key = $this->getKey($value);
        if (array_key_exists($key, $this->items)) {
            unset($this->items[$key]);
            return true;
        }
        return false;
    }

    /**
     * Checks if a given value exists in the set.
     *
     * @param mixed $value The value to check for existence in the set.
     * @return bool Returns true if the value exists in the set, false otherwise.
     */
    public function has(mixed $value): bool
    {
        $key = $this->getKey($value);
        return array_key_exists($key, $this->items);
    }

    /**
     * Clears all elements from the set.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->items = [];
    }

    /**
     * Returns the number of elements in the set.
     *
     * @return int The number of elements in the set.
     */
    public function size(): int
    {
        return count($this->items);
    }

    /**
     * Alias for the size() method. Returns the number of elements in the set.
     * This method is required by the Countable interface.
     *
     * @return int The number of elements in the set.
     */
    public function count(): int
    {
        return $this->size();
    }

    /**
     * Returns an iterator for the set.
     * This allows the set to be used in foreach loops and other iteration contexts.
     *
     * @return ArrayIterator An iterator over the elements of the set.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator(array_values($this->items));
    }

    /**
     * Generates a unique key for a value in the set.
     * For objects, this uses spl_object_hash to generate a unique key.
     * For other types, it serializes the value.
     *
     * @param mixed $value The value to generate a unique key for.
     * @return string The unique key representing the value.
     */
    private function getKey(mixed $value): string
    {
        if (is_object($value)) {
            return spl_object_hash($value);
        }
        return serialize($value);
    }

    /**
     * Creates a new set that is the union of this set and another set.
     * The union contains all unique elements from both sets.
     *
     * @param Set $set The other set to union with.
     * @return Set A new set that contains the union of both sets.
     */
    public function union(Set $set): Set
    {
        $newSet = new Set();
        foreach ($this->items as $item) {
            $newSet->add($item);
        }
        foreach ($set as $item) {
            $newSet->add($item);
        }
        return $newSet;
    }

    /**
     * Creates a new set that is the intersection of this set and another set.
     * The intersection contains only elements that exist in both sets.
     *
     * @param Set $set The other set to intersect with.
     * @return Set A new set that contains the intersection of both sets.
     */
    public function intersection(Set $set): Set
    {
        $newSet = new Set();
        foreach ($this->items as $item) {
            if ($set->has($item)) {
                $newSet->add($item);
            }
        }
        return $newSet;
    }

    /**
     * Creates a new set that is the difference of this set and another set.
     * The difference contains elements that exist in this set but not in the other set.
     *
     * @param Set $set The other set to compare with.
     * @return Set A new set that contains the difference between the two sets.
     */
    public function difference(Set $set): Set
    {
        $newSet = new Set();
        foreach ($this->items as $item) {
            if (!$set->has($item)) {
                $newSet->add($item);
            }
        }
        return $newSet;
    }

    /**
     * Serializes the set object for storage or transmission.
     *
     * @return array An array representation of the set's elements.
     */
    public function __serialize(): array
    {
        return array_values($this->items);
    }

    /**
     * Unserializes the set object from a serialized array.
     *
     * @param array $data The serialized data to restore the set from.
     * @return void
     */
    public function __unserialize(array $data): void
    {
        $this->items = [];
        foreach ($data as $item) {
            $this->add($item);
        }
    }
}

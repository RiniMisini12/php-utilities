<?php

declare(strict_types=1);

namespace Rinimisini\PhpUtilities\Map;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Rinimisini\PhpUtilities\Iterator\SetIterator;

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
     * Returns the custom iterator for the set.
     *
     * @return SetIterator An iterator over the elements of the set.
     */
    public function getIterator(): SetIterator
    {
        return new SetIterator($this->items);
    }

    /**
     * Generates a fast unique key for a value in the set.
     * Uses optimized hashing based on the type of value.
     * For objects, this uses spl_object_hash to generate a unique key.
     * For strings and scalar values, it uses crc32 for fast hashing.
     *
     * @param mixed $value The value to generate a unique key for.
     * @return string The unique key representing the value.
     */
    private function getKey(mixed $value): string
    {
        if (is_object($value)) {
            return spl_object_hash($value);
        }

        if (is_array($value)) {
            return $this->hashArray($value);
        }

        if (is_scalar($value)) {
            return (string) crc32((string) $value);
        }

        return serialize($value);
    }

    /**
     * Recursively hashes an array to produce a unique key string.
     *
     * @param array $array The array to hash.
     * @return string The hashed string representing the array.
     */
    private function hashArray(array $array): string
    {
        $hashes = [];
        foreach ($array as $key => $value) {
            $hashes[] = $this->getKey($key) . '=>' . $this->getKey($value);
        }

        return hash('crc32b', implode(',', $hashes));
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
     * Returns an array of the objects in the Set.
     *
     * @return array<object> An array of objects in the S.
     */
    public function toArray(): array
    {
        $objects = [];
        foreach ($this->items as $object => $_) {
            $objects[] = $object;
        }
        return $objects;
    }

    /**
     * Checks if this set is a subset of another set.
     */
    public function isSubsetOf(Set $set): bool
    {
        foreach ($this as $item) {
            if (!$set->has($item)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if this set is a superset of another set.
     */
    public function isSupersetOf(Set $set): bool
    {
        return $set->isSubsetOf($this);
    }

    /**
     * Checks if this set is equal to another set.
     */
    public function isEqual(Set $set): bool
    {
        return $this->size() === $set->size() && $this->isSubsetOf($set);
    }

    /**
     * Performs a callback for each element in the Set.
     *
     * @param callable $callback The callback function to execute. Receives the element as a parameter.
     * @return void
     */
    public function forEach(callable $callback): void
    {
        foreach ($this->items as $item) {
            $callback($item);
        }
    }

    /**
     * Checks if the Set is empty.
     *
     * @return bool True if the Set is empty, false otherwise.
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }


    /**
     * Serializes the set object for storage or transmission.
     *
     * @return array An array representation of the set's elements.
     */
    public function __serialize(): array
    {
        $serializedItems = [];

        foreach ($this->items as $key => $value) {
            $serializedItems[$key] = is_object($value) ? serialize($value) : $value;
        }

        return $serializedItems;
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

        foreach ($data as $key => $value) {
            $this->items[$key] = is_string($value) && strpos($value, 'O:') === 0 ? unserialize($value) : $value;
        }
    }
}

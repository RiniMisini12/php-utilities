<?php

declare(strict_types=1);

namespace Rinimisini\PhpUtilities;

use Countable;
use IteratorAggregate;
use ArrayIterator;
use WeakMap;

/**
 * A PHP implementation of JavaScript's WeakSet.
 * Stores objects weakly, allowing them to be garbage collected if there are no other references.
 */
class WeakSet implements Countable, IteratorAggregate
{
    /**
     * @var WeakMap<object, bool> Underlying storage for the WeakSet.
     */
    private WeakMap $weakMap;

    /**
     * Constructs a new WeakSet instance.
     *
     * @param iterable<object> $objects Optional initial objects to include in the WeakSet.
     */
    public function __construct(iterable $objects = [])
    {
        $this->weakMap = new WeakMap();
        foreach ($objects as $object) {
            $this->add($object);
        }
    }

    /**
     * Adds an object to the WeakSet.
     *
     * @param object $object The object to add.
     * @return self Returns the WeakSet instance for method chaining.
     */
    public function add(object $object): self
    {
        $this->weakMap[$object] = true;
        return $this;
    }

    /**
     * Checks if the WeakSet contains the specified object.
     *
     * @param object $object The object to check for.
     * @return bool True if the object is in the WeakSet, false otherwise.
     */
    public function has(object $object): bool
    {
        return isset($this->weakMap[$object]);
    }

    /**
     * Removes the specified object from the WeakSet.
     *
     * @param object $object The object to remove.
     * @return bool True if the object was in the WeakSet and has been removed, false if it was not found.
     */
    public function delete(object $object): bool
    {
        if (isset($this->weakMap[$object])) {
            unset($this->weakMap[$object]);
            return true;
        }
        return false;
    }

    /**
     * Removes all objects from the WeakSet.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->weakMap = new WeakMap();
    }

    /**
     * Returns the number of objects currently in the WeakSet.
     * Note: This count can change if objects are garbage collected.
     *
     * @return int The number of objects in the WeakSet.
     */
    public function count(): int
    {
        return count($this->weakMap);
    }

    /**
     * Returns an iterator over the objects in the WeakSet.
     *
     * @return ArrayIterator<object> An iterator over the objects.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->toArray());
    }

    /**
     * Performs a callback for each object in the WeakSet.
     *
     * @param callable $callback The callback function to execute. Receives the object as a parameter.
     * @return void
     */
    public function forEach(callable $callback): void
    {
        foreach ($this->weakMap as $object => $_) {
            $callback($object);
        }
    }

    /**
     * Checks if the WeakSet is empty.
     *
     * @return bool True if the WeakSet is empty, false otherwise.
     */
    public function isEmpty(): bool
    {
        return count($this->weakMap) === 0;
    }

    /**
     * Returns an array of the objects in the WeakSet.
     *
     * @return array<object> An array of objects in the WeakSet.
     */
    public function toArray(): array
    {
        $objects = [];
        foreach ($this->weakMap as $object => $_) {
            $objects[] = $object;
        }
        return $objects;
    }

    /**
     * Creates a new WeakSet that is the union of this WeakSet and another WeakSet.
     * The union contains all unique objects from both WeakSets.
     *
     * @param WeakSet $set The other WeakSet to union with.
     * @return WeakSet A new WeakSet that contains the union of both WeakSets.
     */
    public function union(WeakSet $set): WeakSet
    {
        $newSet = new WeakSet();
        foreach ($this as $object) {
            $newSet->add($object);
        }
        foreach ($set as $object) {
            $newSet->add($object);
        }
        return $newSet;
    }

    /**
     * Creates a new WeakSet that is the intersection of this WeakSet and another WeakSet.
     * The intersection contains only objects that exist in both WeakSets.
     *
     * @param WeakSet $set The other WeakSet to intersect with.
     * @return WeakSet A new WeakSet that contains the intersection of both WeakSets.
     */
    public function intersection(WeakSet $set): WeakSet
    {
        $newSet = new WeakSet();
        foreach ($this as $object) {
            if ($set->has($object)) {
                $newSet->add($object);
            }
        }
        return $newSet;
    }

    /**
     * Creates a new WeakSet that is the difference of this WeakSet and another WeakSet.
     * The difference contains objects that exist in this WeakSet but not in the other WeakSet.
     *
     * @param WeakSet $set The other WeakSet to compare with.
     * @return WeakSet A new WeakSet that contains the difference between the two WeakSets.
     */
    public function difference(WeakSet $set): WeakSet
    {
        $newSet = new WeakSet();
        foreach ($this as $object) {
            if (!$set->has($object)) {
                $newSet->add($object);
            }
        }
        return $newSet;
    }

    /**
     * Checks if this WeakSet is a subset of another WeakSet.
     *
     * @param WeakSet $set The WeakSet to compare with.
     * @return bool True if this WeakSet is a subset of the other, false otherwise.
     */
    public function isSubsetOf(WeakSet $set): bool
    {
        foreach ($this as $object) {
            if (!$set->has($object)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if this WeakSet is a superset of another WeakSet.
     *
     * @param WeakSet $set The WeakSet to compare with.
     * @return bool True if this WeakSet is a superset of the other, false otherwise.
     */
    public function isSupersetOf(WeakSet $set): bool
    {
        return $set->isSubsetOf($this);
    }

    /**
     * Checks if this WeakSet is equal to another WeakSet.
     *
     * @param WeakSet $set The WeakSet to compare with.
     * @return bool True if both WeakSets are equal, false otherwise.
     */
    public function isEqual(WeakSet $set): bool
    {
        return $this->count() === $set->count() && $this->isSubsetOf($set);
    }
}

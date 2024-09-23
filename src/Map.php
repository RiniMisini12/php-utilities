<?php

declare(strict_types=1);

namespace Rinimisini\PhpUtilities;

use Countable;
use IteratorAggregate;
use ArrayIterator;
use JsonException;

/**
 * A PHP implementation of JavaScript's Map.
 * Stores key-value pairs and maintains the order of insertion.
 * Keys can be of any type, including objects.
 */
class Map implements Countable, IteratorAggregate
{
    /**
     * @var array<int, array{0: mixed, 1: mixed}> Internal storage for the Map entries.
     */
    private array $entries = [];

    /**
     * @var array<string, int> Mapping of keys to their position in the entries array.
     */
    private array $keyIndexMap = [];

    /**
     * Adds or updates an entry in the Map.
     *
     * @param mixed $key The key of the element to add.
     * @param mixed $value The value of the element to add.
     * @return self Returns the Map instance for method chaining.
     *
     * @throws JsonException
     */
    public function set(mixed $key, mixed $value): self
    {
        $keyHash = $this->getKeyHash($key);

        if (isset($this->keyIndexMap[$keyHash])) {
            // Update existing entry
            $index = $this->keyIndexMap[$keyHash];
            $this->entries[$index][1] = $value;
        } else {
            // Add new entry
            $this->entries[] = [$key, $value];
            $this->keyIndexMap[$keyHash] = array_key_last($this->entries);
        }

        return $this;
    }

    /**
     * Retrieves the value associated with the given key.
     *
     * @param mixed $key The key of the element to retrieve.
     * @return mixed|null The value associated with the key, or null if not found.
     *
     * @throws JsonException
     */
    public function get(mixed $key): mixed
    {
        $keyHash = $this->getKeyHash($key);

        if (isset($this->keyIndexMap[$keyHash])) {
            $index = $this->keyIndexMap[$keyHash];
            return $this->entries[$index][1];
        }

        return null;
    }

    /**
     * Checks if the Map contains the given key.
     *
     * @param mixed $key The key to check for.
     * @return bool True if the key exists in the Map, false otherwise.
     *
     * @throws JsonException
     */
    public function has(mixed $key): bool
    {
        $keyHash = $this->getKeyHash($key);
        return isset($this->keyIndexMap[$keyHash]);
    }

    /**
     * Removes the entry associated with the given key.
     *
     * @param mixed $key The key of the element to remove.
     * @return bool True if an element was removed, false if the key was not found.
     *
     * @throws JsonException
     */
    public function delete(mixed $key): bool
    {
        $keyHash = $this->getKeyHash($key);

        if (isset($this->keyIndexMap[$keyHash])) {
            $index = $this->keyIndexMap[$keyHash];
            unset($this->entries[$index], $this->keyIndexMap[$keyHash]);

            // Reindex entries and keyIndexMap
            $this->entries = array_values($this->entries);
            $this->rebuildKeyIndexMap();

            return true;
        }

        return false;
    }

    /**
     * Removes all entries from the Map.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->entries = [];
        $this->keyIndexMap = [];
    }

    /**
     * Returns the number of entries in the Map.
     *
     * @return int The number of entries.
     */
    public function count(): int
    {
        return count($this->entries);
    }

    /**
     * Returns the number of entries in the Map.
     *
     * @return int The number of entries.
     */
    public function size(): int
    {
        return $this->count();
    }

    /**
     * Returns an iterator over the Map entries.
     * Each iteration yields a key-value pair array: [key, value].
     *
     * @return ArrayIterator<int, array{0: mixed, 1: mixed}> The iterator over entries.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->entries);
    }

    /**
     * Executes a callback for each entry in the Map.
     *
     * @param callable $callback The callback function, which receives key and value as parameters.
     * @return void
     */
    public function forEach(callable $callback): void
    {
        foreach ($this->entries as [$key, $value]) {
            $callback($value, $key);
        }
    }

    /**
     * Returns an array of all keys in the Map.
     *
     * @return array<int, mixed> The array of keys.
     */
    public function keys(): array
    {
        return array_map(fn($entry) => $entry[0], $this->entries);
    }

    /**
     * Returns an array of all values in the Map.
     *
     * @return array<int, mixed> The array of values.
     */
    public function values(): array
    {
        return array_map(fn($entry) => $entry[1], $this->entries);
    }

    /**
     * Returns an array of all entries in the Map.
     *
     * @return array<int, array{0: mixed, 1: mixed}> The array of entries.
     */
    public function entries(): array
    {
        return $this->entries;
    }

    /**
     * Generates a unique hash for the given key.
     *
     * @param mixed $key The key to generate a hash for.
     * @return string The unique hash representing the key.
     *
     * @throws JsonException
     */
    private function getKeyHash(mixed $key): string
    {
        if (is_object($key)) {
            return spl_object_hash($key);
        }

        return json_encode($key, JSON_THROW_ON_ERROR);
    }

    /**
     * Rebuilds the key index map after entries have been reindexed.
     *
     * @return void
     *
     * @throws JsonException
     */
    private function rebuildKeyIndexMap(): void
    {
        $this->keyIndexMap = [];
        foreach ($this->entries as $index => [$key, $value]) {
            $keyHash = $this->getKeyHash($key);
            $this->keyIndexMap[$keyHash] = $index;
        }
    }
}

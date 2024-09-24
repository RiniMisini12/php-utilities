<?php

declare(strict_types=1);

namespace Rinimisini\PhpUtilities\Map;

use Countable;
use IteratorAggregate;
use ArrayIterator;
use Exception;
use SplDoublyLinkedList;

/**
 * A HashMap implementation for PHP, inspired by Java's HashMap.
 * Stores key-value pairs and allows any type of key.
 */
class HashMap implements Countable, IteratorAggregate
{
    /**
     * Default initial capacity for the bucket array.
     */
    private const INITIAL_CAPACITY = 17;

    /**
     * The load factor determines when the HashMap will be resized (default: 0.75).
     */
    private const LOAD_FACTOR = 0.75;

    /**
     * The bucket array that stores key-value pairs.
     * @var array<SplDoublyLinkedList> Each bucket is a linked list of key-value pairs.
     */
    private array $buckets = [];

    /**
     * The number of key-value pairs in the HashMap.
     * @var int
     */
    private int $size = 0;

    public function __construct(int $capacity = self::INITIAL_CAPACITY)
    {
        $this->buckets = array_fill(0, $capacity, new SplDoublyLinkedList());
    }

    /**
     * Adds a key-value pair to the HashMap.
     * If the key already exists, its value is updated.
     *
     * @param mixed $key The key for the map.
     * @param mixed $value The value to store.
     * @return self Returns the HashMap instance for chaining.
     * @throws Exception If the key is not hashable.
     */
    public function set(mixed $key, mixed $value): self
    {
        $this->resizeIfNeeded();
        $bucketIndex = $this->getBucketIndex($key);

        $bucket = $this->buckets[$bucketIndex];

        for ($bucket->rewind(); $bucket->valid(); $bucket->next()) {
            $entry = $bucket->current();
            if ($this->keysAreEqual($entry['key'], $key)) {
                $entry['value'] = $value;

                return $this;
            }
        }

        $bucket->push(['key' => $key, 'value' => $value]);
        $this->size++;

        return $this;
    }

    /**
     * Retrieves a value by key.
     *
     * @param mixed $key The key to look up.
     * @return mixed|null The value if found, or null if not.
     * @throws Exception If the key is not hashable.
     */
    public function get(mixed $key): mixed
    {
        $bucketIndex = $this->getBucketIndex($key);

        $bucket = $this->buckets[$bucketIndex];

        for ($bucket->rewind(); $bucket->valid(); $bucket->next()) {
            $entry = $bucket->current();
            if ($this->keysAreEqual($entry['key'], $key)) {
                return $entry['value'];
            }
        }

        return null;
    }

    /**
     * Deletes a key-value pair by key.
     *
     * @param mixed $key The key to delete.
     * @return bool True if the key was deleted, false if the key was not found.
     * @throws Exception If the key is not hashable.
     */
    public function delete(mixed $key): bool
    {
        $bucketIndex = $this->getBucketIndex($key);
        $bucket = $this->buckets[$bucketIndex];

        for ($bucket->rewind(); $bucket->valid(); $bucket->next()) {
            $entry = $bucket->current();
            if ($this->keysAreEqual($entry['key'], $key)) {
                $bucket->offsetUnset($bucket->key());
                $this->size--;

                return true;
            }
        }

        return false;
    }

    /**
     * Clears the HashMap, removing all key-value pairs.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->buckets = array_fill(0, count($this->buckets), new SplDoublyLinkedList());
        $this->size = 0;
    }

    /**
     * Returns the number of key-value pairs in the HashMap.
     *
     * @return int The number of key-value pairs.
     */
    public function count(): int
    {
        return $this->size;
    }

    /**
     * Provides an iterator for the HashMap, allowing foreach iteration.
     *
     * @return ArrayIterator An iterator over the key-value pairs.
     */
    public function getIterator(): ArrayIterator
    {
        $flatArray = [];
        foreach ($this->buckets as $bucket) {
            for ($bucket->rewind(); $bucket->valid(); $bucket->next()) {
                $flatArray[] = $bucket->current();
            }
        }

        return new ArrayIterator($flatArray);
    }

    /**
     * Calculates the index of the bucket based on the hash of the key.
     *
     * @param mixed $key The key to hash.
     * @return int The index of the bucket.
     * @throws Exception If the key is not hashable.
     */
    private function getBucketIndex(mixed $key): int
    {
        $hash = $this->getHash($key);

        return abs(crc32($hash) % count($this->buckets));
    }

    /**
     * Generates a unique hash for the key.
     *
     * @param mixed $key The key to hash.
     * @return string The unique hash for the key.
     * @throws Exception If the key is not hashable.
     */
    private function getHash(mixed $key): string
    {
        if (is_object($key)) {
            return spl_object_hash($key);
        }

        if (is_array($key)) {
            return $this->hashArray($key);
        }

        if (is_scalar($key) || is_null($key)) {
            return var_export($key, true);
        }

        throw new Exception('Key is not hashable.');
    }

    /**
     * Recursively hashes an array to produce a unique hash string.
     *
     * @param array $array The array to hash.
     * @return string The hash string.
     *
     * @throws Exception
     */
    private function hashArray(array $array): string
    {
        $parts = [];
        foreach ($array as $key => $value) {
            $keyHash = $this->getHash($key);
            $valueHash = $this->getHash($value);
            $parts[] = $keyHash . '=>' . $valueHash;
        }

        return md5(implode(',', $parts));
    }

    /**
     * Compares two keys for equality.
     *
     * @param mixed $key1
     * @param mixed $key2
     * @return bool
     */
    private function keysAreEqual(mixed $key1, mixed $key2): bool
    {
        return $key1 === $key2;
    }

    /**
     * Resizes the HashMap when the load factor exceeds the threshold.
     *
     * @return void
     * @throws Exception
     */
    private function resizeIfNeeded(): void
    {
        if ($this->size / count($this->buckets) > self::LOAD_FACTOR) {
            $this->resize();
        }
    }

    /**
     * Doubles the size of the bucket array and rehashes all keys.
     *
     * @return void
     * @throws Exception
     */
    private function resize(): void
    {
        $newCapacity = count($this->buckets) * 2;
        $oldBuckets = $this->buckets;
        $this->buckets = array_fill(0, $newCapacity, new SplDoublyLinkedList());
        $this->size = 0;

        foreach ($oldBuckets as $bucket) {
            for ($bucket->rewind(); $bucket->valid(); $bucket->next()) {
                $entry = $bucket->current();
                $this->set($entry['key'], $entry['value']);
            }
        }
    }
}
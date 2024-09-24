<?php

declare(strict_types=1);

namespace Rinimisini\PhpUtilities\Tests\Map;

use PHPUnit\Framework\TestCase;
use Rinimisini\PhpUtilities\Map\Set;

class SetStressTest extends TestCase
{
    /**
     * Stress test the Set with a large number of elements.
     */
    public function testStressTestSet(): void
    {
        $set = new Set();
        $numElements = 10000;

        for ($i = 0; $i < $numElements; $i++) {
            $set->add($i);
        }

        $this->assertSame($numElements, $set->size());

        for ($i = 0; $i < $numElements; $i++) {
            $this->assertTrue($set->has($i));
        }

        for ($i = 0; $i < $numElements / 2; $i++) {
            $set->delete($i);
        }

        $this->assertSame($numElements / 2, $set->size());

        for ($i = $numElements / 2; $i < $numElements; $i++) {
            $this->assertTrue($set->has($i));
        }

        for ($i = 0; $i < $numElements / 2; $i++) {
            $this->assertFalse($set->has($i));
        }
    }
}

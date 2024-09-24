<?php

declare(strict_types=1);

namespace Rinimisini\PhpUtilities\Tests\Map;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rinimisini\PhpUtilities\Map\Set;
use Rinimisini\PhpUtilities\Iterator\SetIterator;
use stdClass;

class SetTest extends TestCase
{
    #[Test]
    public function addElements(): void
    {
        // arrange
        $set = new Set();
        $set->add(1);
        $set->add(2);
        $set->add(2);

        $this->assertTrue($set->has(1));
        $this->assertTrue($set->has(2));
        $this->assertSame(2, $set->size());

        $obj1 = new stdClass();
        $obj2 = new stdClass();
        $arr1 = ['key' => 'value'];
        $arr2 = ['anotherKey' => 'anotherValue'];

        // act
        $set->add($obj1);
        $set->add($arr1);
        $set->add($obj1);
        $set->add($arr2);

        // assert
        $this->assertTrue($set->has($obj1));
        $this->assertTrue($set->has($arr1));
        $this->assertTrue($set->has($arr2));
        $this->assertSame(5, $set->size());
    }

    #[Test]
    public function deleteElements(): void
    {
        $set = new Set();
        $set->add(1);
        $set->add(2);

        $obj = new stdClass();
        $set->add($obj);

        $set->delete(1);
        $set->delete($obj);

        $this->assertFalse($set->has(1));
        $this->assertTrue($set->has(2));
        $this->assertFalse($set->has($obj));
        $this->assertSame(1, $set->size());
    }

    #[Test]
    public function clearSet(): void
    {
        $set = new Set();
        $set->add(1);
        $set->add(2);
        $set->add([1, 2, 3]);
        $set->add(new stdClass());

        $set->clear();

        $this->assertTrue($set->isEmpty());
        $this->assertSame(0, $set->size());
    }

    #[Test]
    public function customIterator(): void
    {
        $set = new Set();
        $set->add(1);
        $set->add(2);
        $set->add(3);

        $iterator = $set->getIterator();
        $this->assertInstanceOf(SetIterator::class, $iterator);

        $values = [];
        foreach ($iterator as $value) {
            $values[] = $value;
        }

        $this->assertSame([1, 2, 3], $values);
    }

    #[Test]
    public function unionOfSets(): void
    {
        $set1 = new Set();
        $set1->add(1);
        $set1->add(2);

        $set2 = new Set();
        $set2->add(2);
        $set2->add(3);

        $obj = new stdClass();
        $set1->add($obj);
        $set2->add($obj); // Add same object to both sets

        $unionSet = $set1->union($set2);

        $this->assertSame(4, $unionSet->size());
        $this->assertTrue($unionSet->has(1));
        $this->assertTrue($unionSet->has(2));
        $this->assertTrue($unionSet->has(3));
        $this->assertTrue($unionSet->has($obj));
    }

    #[Test]
    public function intersectionOfSets(): void
    {
        $set1 = new Set();
        $set1->add(1);
        $set1->add(2);

        $set2 = new Set();
        $set2->add(2);
        $set2->add(3);

        $obj = new stdClass();
        $set1->add($obj);
        $set2->add($obj); // Add same object to both sets

        $intersectionSet = $set1->intersection($set2);

        $this->assertSame(2, $intersectionSet->size()); // Only 2 and the object should intersect
        $this->assertTrue($intersectionSet->has(2));
        $this->assertTrue($intersectionSet->has($obj));
        $this->assertFalse($intersectionSet->has(1));
        $this->assertFalse($intersectionSet->has(3));
    }

    #[Test]
    public function differenceBetweenSets(): void
    {
        $set1 = new Set();
        $set1->add(1);
        $set1->add(2);

        $set2 = new Set();
        $set2->add(2);
        $set2->add(3);

        $arr1 = ['key1' => 'value1'];
        $arr2 = ['key2' => 'value2'];
        $set1->add($arr1);
        $set2->add($arr2);

        $differenceSet = $set1->difference($set2);

        $this->assertSame(2, $differenceSet->size()); // 1 and $arr1 should be in the difference
        $this->assertTrue($differenceSet->has(1));
        $this->assertTrue($differenceSet->has($arr1));
        $this->assertFalse($differenceSet->has(2));
        $this->assertFalse($differenceSet->has(3));
        $this->assertFalse($differenceSet->has($arr2));
    }

    #[Test]
    public function serializationOfSet(): void
    {
        $set = new Set();
        $set->add(1);
        $set->add(2);

        $obj = new stdClass();
        $set->add($obj);

        $serialized = serialize($set);
        $unserializedSet = unserialize($serialized);

        $this->assertInstanceOf(Set::class, $unserializedSet);
        $this->assertTrue($unserializedSet->has(1));
        $this->assertTrue($unserializedSet->has(2));
        $this->assertTrue($unserializedSet->has($obj));
    }


    #[Test]
    public function handlesComplexDataTypes(): void
    {
        $set = new Set();

        $object1 = new stdClass();
        $object1->name = 'Object1';

        $object2 = new stdClass();
        $object2->name = 'Object2';

        $array1 = ['a' => 1, 'b' => 2];
        $array2 = ['x' => 100, 'y' => 200];

        $set->add($object1);
        $set->add($object2);
        $set->add($array1);
        $set->add($array2);

        $this->assertTrue($set->has($object1));
        $this->assertTrue($set->has($object2));
        $this->assertTrue($set->has($array1));
        $this->assertTrue($set->has($array2));
        $this->assertSame(4, $set->size());

        $set->add($object1);
        $this->assertSame(4, $set->size());
    }
}
# PHP Utilities

PHP Utilities is a PHP library that provides implementations of various JavaScript built-in classes like `Set`, `Map`, and others. This library allows PHP developers to work with similar functionality found in JavaScript, but adapted for PHP environments.

## Features

- **Set Class**: A PHP implementation of JavaScript's `Set` class, providing methods for adding unique elements, deleting elements, performing set operations (union, intersection, and difference), and more.
- Future updates will include more classes such as:
    - **Map**
    - **WeakSet**
    - **WeakMap**

## Requirements

- **PHP**: ^8.0

## Installation

You can install this package via Composer:

```bash
composer require rinimisini/php-utilities
```

## Usage
### Set Class
The Set class behaves similarly to JavaScript's Set. It allows storing unique elements and provides methods to perform common set operations.

#### Creating a Set
```php
$set = new Set();
```
#### Adding Elements to the Set

```php
$set->add(1);
$set->add(2);
$set->add(3);
```
#### Checking if an Element Exists

```php
if ($set->has(2)) {
echo "Set contains 2";
}
```
#### Deleting an Element
```php
$set->delete(2);
```
#### Clearing the Set

```php
$set->clear();
```
#### Getting the Set Size

```php
echo $set->size();  // Outputs the number of elements in the set
```

### Set Operations
The Set class supports common set operations like union, intersection, and difference.

#### Union
```php
$set1 = new Set();
$set1->add(1)->add(2);

$set2 = new Set();
$set2->add(2)->add(3);

$unionSet = $set1->union($set2);  // $unionSet contains [1, 2, 3]
```

#### Intersection

```php
$set1 = new Set();
$set1->add(1)->add(2);

$set2 = new Set();
$set2->add(2)->add(3);

$intersectionSet = $set1->intersection($set2);  // $intersectionSet contains [2]
```
#### Difference

```php
$set1 = new Set();
$set1->add(1)->add(2);

$set2 = new Set();
$set2->add(2)->add(3);

$differenceSet = $set1->difference($set2);  // $differenceSet contains [1]
```
#### Iterating Over the Set

```php
foreach ($set as $item) {
echo $item;
}
```
#### Serialization

```php
$set = new Set();
$set->add(1)->add(2);

$serialized = serialize($set);
$unserializedSet = unserialize($serialized);
```

## Map Class
The Map class behaves similarly to JavaScript's Map. It allows storing key-value pairs where keys can be of any type, including objects.

### Creating a Map
```php
$map = new Map();
```

#### Setting Key-Value Pairs

```php
$map->set('name', 'Alice');
$map->set('age', 30);
```

#### Retrieving Values

```php
echo $map->get('name');  // Outputs: Alice
```

#### Checking if a Key Exists

```php
if ($map->has('age')) {
echo "Age is set in the map.";
}
```

#### Deleting an Entry

```php
$map->delete('age');  // Removes the entry with key 'age'
```

#### Iterating Over the Map

```php
foreach ($map as [$key, $value]) {
echo "Key: $key, Value: $value\n";
}
```

#### Using forEach

```php
$map->forEach(function ($value, $key) {
echo "Key: $key, Value: $value\n";
});
```

#### Retrieving Keys and Values

```php
$keys = $map->keys();     // Returns an array of keys
$values = $map->values(); // Returns an array of values
```

#### Clearing the Map

```php
$map->clear();
``` 

## WeakSet Class

The WeakSet class allows storing objects weakly, meaning they will be garbage collected when no other references exist.

#### Creating a WeakSet
```php
$weakSet = new WeakSet();
```

#### Adding Objects to the WeakSet

```php
$obj = new stdClass();
$weakSet->add($obj);
```

#### Checking if an Object Exists

```php
if ($weakSet->has($obj)) {
echo "Object exists in the WeakSet";
}
```

#### Removing an Object

```php
$weakSet->delete($obj);
```

#### Clearing the WeakSet

```php
$weakSet->clear();
```

#### Iterating Over the WeakSet

```php
foreach ($weakSet as $object) {
echo get_class($object);
}
```

## Future Plans
#### The current release includes JavaScript-inspired classes such as:
- Map: A collection of key-value pairs with unique keys.
- WeakSet: A collection that stores objects weakly, allowing them to be garbage collected if no other references exist.

### Future releases may include additional JavaScript-inspired classes such as:
- WeakMap: A collection of key-value pairs where the keys are objects and references are weak.

## Contributing
Contributions are welcome! If you'd like to contribute, feel free to open a pull request or submit issues for features and bug reports.

### Steps to Contribute

1. Fork this repository.
2. Create a new feature branch: `git checkout -b feature/my-feature`.
3. Commit your changes: `git commit -m 'Add some feature'`.
4. Push the branch: `git push origin feature/my-feature`.
5. Open a Pull Request.


## License
This project is licensed under the **MIT** License. See the [LICENSE](LICENSE) file for more details.

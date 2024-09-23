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
###  Set Class
The Set class behaves similarly to JavaScript's Set. It allows storing unique elements and provides methods to perform common set operations.

#### Creating a Set

```php
$set = new Set();
// Adding Elements to the Set

$set->add(1);
$set->add(2);
$set->add(3);

// Checking if an Element Exists

if ($set->has(2)) {
echo "Set contains 2";
}

// Deleting an Element

$set->delete(2);

// Clearing the Set

$set->clear();

// Getting the Set Size

echo $set->size();  // Outputs the number of elements in the set

/*
 * Set Operations
 * The Set class supports common set operations like union, intersection, and difference.
*/

// Union

$set1 = new Set();
$set1->add(1)->add(2);

$set2 = new Set();
$set2->add(2)->add(3);

$unionSet = $set1->union($set2);  // $unionSet contains [1, 2, 3]

// Intersection

$set1 = new Set();
$set1->add(1)->add(2);

$set2 = new Set();
$set2->add(2)->add(3);

$intersectionSet = $set1->intersection($set2);  // $intersectionSet contains [2]

// Difference

$set1 = new Set();
$set1->add(1)->add(2);

$set2 = new Set();
$set2->add(2)->add(3);

$differenceSet = $set1->difference($set2);  // $differenceSet contains [1]

// Iterating Over the Set

foreach ($set as $item) {
echo $item;
}

/*
 * Serialization
 * The Set class also supports serialization and unserialization.
 */
 
$set = new Set();
$set->add(1)->add(2);

$serialized = serialize($set);
$unserializedSet = unserialize($serialized);
```
## Future Plans
#### In future releases, we plan to implement other JavaScript-inspired classes such as:

- Map: A collection of key-value pairs with unique keys.

## Contributing
Contributions are welcome! If you'd like to contribute, feel free to open a pull request or submit issues for features and bug reports.

#### Steps to Contribute:
- Fork this repository.
- Create a new feature branch (git checkout -b feature/my-feature).
- Commit your changes (git commit -m 'Add some feature').
- Push the branch (git push origin feature/my-feature).
- Open a Pull Request.

## License
This project is licensed under the MIT License. See the LICENSE file for more details.
## SemVer Parser

[![Build Status](https://travis-ci.com/stevegrunwell/semver-parser.svg?branch=develop)](https://travis-ci.com/stevegrunwell/semver-parser)

This library defines the `SteveGrunwell\SemVer\Version` class, which is meant to parse and manipulate version numbers based on [the rules of Semantic Versioning (a.k.a. "SemVer")](https://semver.org/).

## Installation

Install the library using [Composer](https://getcomposer.org/):

```sh
$ composer require stevegrunwell/semver-parser
```

## Usage

The constructor of the `Version` class can accept a valid, semantic version based on [the Semantic Versioning 2.0.0 specification](https://semver.org/spec/v2.0.0.html):

```php
use SteveGrunwell\SemVer\Parser;

// Import the Composer-generated autoloader.
require_once __DIR__ . '/vendor/autoload.php';

$version = new Version('1.2.3-rc1');

// Parse the version.
$version->getMajorVersion();      // 1
$version->getMinorVersion();      // 2
$version->getPatchVersion();      // 3
$version->getPreReleaseVersion(); // rc1

// Modify the version.
$version->setMajorVersion(4);
$version->setMinorVersion(5);
$version->setPatchVersion(6);
$version->setPreReleaseVersion('rc2');

// Retrieve the updated version as a string.
$version->getVersion(); // "4.5.6-rc2"
(string) $version;      // "4.5.6-rc2"
```

### Additional methods

In addition to the setters and getters described above, each of the major, minor, and patch values have corresponding increment and decrement methods:

```php
// Increment values.
$version->incrementMajorVersion();
$version->incrementMinorVersion();
$version->incrementPatchVersion();

// Decrement values.
$version->decrementMajorVersion();
$version->decrementMinorVersion();
$version->decrementPatchVersion();
```

It's worth noting that `incrementMajorVersion()` and `incrementMinorVersion()` will reset the the minor/patch and patch numbers (respectively) according to [the Semantic Versioning 2.0.0 specification](https://semver.org/spec/v2.0.0.html#spec-item-7).

## License

This library is released under the MIT License. Please see [LICENSE.md](LICENSE.md) for more details.

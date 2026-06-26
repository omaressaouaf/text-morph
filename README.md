# TextMorph

[![Latest Stable Version](https://img.shields.io/packagist/v/omaressaouaf/text-morph.svg)](https://packagist.org/packages/omaressaouaf/text-morph)
[![License](https://img.shields.io/github/license/omaressaouaf/text-morph)](LICENSE)
[![Tests](https://github.com/omaressaouaf/text-morph/actions/workflows/tests.yml/badge.svg)](https://github.com/omaressaouaf/text-morph/actions/workflows/tests.yml)

A lightweight PHP package for **reversible text transformations** using simple morphing algorithms and a composable pipeline.

## Features

- **Rotational** morphing: shift letters across a 52-character alphabet with wrap-around
- **Substitution** morphing: bidirectional character-pair swaps with case preservation
- **Transposition** morphing: rail-fence (zigzag) character reordering
- **Composite** pipeline: chain multiple morphers and reverse them in one call
- Every morpher supports `morph()` and `unmorph()`
- Framework-agnostic, zero runtime dependencies

> TextMorph is a text transformation package. It is not intended for encryption, password hashing, or security-sensitive data.

---

## Installation

Install via Composer:

```sh
composer require omaressaouaf/text-morph
```

---

## Usage

Every morpher implements the `TextMorpher` contract:

```php
use Omaressaouaf\TextMorph\Contracts\TextMorpher;

interface TextMorpher
{
    public function morph(string $text): string;

    public function unmorph(string $text): string;
}
```

### Rotational Morphing

Shift each letter forward across `a-zA-Z`, wrapping from `Z` back to `a`. Non-alphabetic characters are left unchanged.

```php
use Omaressaouaf\TextMorph\Morphers\RotationalTextMorpher;

$morpher = new RotationalTextMorpher(3);

$morphed = $morpher->morph('Meet at 9pm!');
// Phhw dw 9sp!

$original = $morpher->unmorph($morphed);
// Meet at 9pm!
```

More examples:

```php
$morpher = new RotationalTextMorpher(1);

$morpher->morph('a'); // b
$morpher->morph('z'); // B
$morpher->morph('Z'); // a
```

### Substitution Morphing

Swap character pairs bidirectionally. Each pair defines a two-way substitution that preserves letter case.

```php
use Omaressaouaf\TextMorph\Morphers\SubstitutionTextMorpher;

$morpher = new SubstitutionTextMorpher(['ab', 'cd']);

$morpher->morph('aabbcc'); // bbaacc
$morpher->morph('adam');   // bcbm
```

Because pairs are swaps, `unmorph()` applies the same transform as `morph()`:

```php
$morpher->unmorph('bbaacc'); // aabbcc
```

### Transposition Morphing

Reorder characters using a rail-fence (zigzag) cipher. Letters are written in a zigzag across the configured number of rails, then read row by row.

```php
use Omaressaouaf\TextMorph\Morphers\TranspositionTextMorpher;

$morpher = new TranspositionTextMorpher(3);

$morpher->morph('HELLO');       // HOELL
$morpher->morph('Meet at 9pm!'); // M 9eta p!etm
```

`unmorph()` splits the text back into rails and reads the zigzag to restore the original order.

### Composite Pipeline

Chain multiple morphers together. `morph()` applies them in registration order; `unmorph()` reverses them.

```php
use Omaressaouaf\TextMorph\Morphers\CompositeTextMorpher;
use Omaressaouaf\TextMorph\Morphers\RotationalTextMorpher;
use Omaressaouaf\TextMorph\Morphers\SubstitutionTextMorpher;
use Omaressaouaf\TextMorph\Morphers\TranspositionTextMorpher;

$pipeline = new CompositeTextMorpher();

$pipeline->add(new SubstitutionTextMorpher(['ae', 'io']));
$pipeline->add(new RotationalTextMorpher(5));
$pipeline->add(new TranspositionTextMorpher(3));

$original = 'Meet at 9pm!';

$morphed = $pipeline->morph($original);
$restored = $pipeline->unmorph($morphed);

// $restored === $original
```

---

### Custom Morphers

The package is fully extensible. You can create your own morphers by implementing the `TextMorpher` contract.

#### Example

```php
namespace App\Morphers;

use Omaressaouaf\TextMorph\Contracts\TextMorpher;

class ReverseTextMorpher implements TextMorpher
{
    public function morph(string $text): string
    {
        return strrev($text);
    }

    public function unmorph(string $text): string
    {
        return strrev($text);
    }
}
```

Then use it on its own or inside a composite pipeline:

```php
use App\Morphers\ReverseTextMorpher;
use Omaressaouaf\TextMorph\Morphers\CompositeTextMorpher;
use Omaressaouaf\TextMorph\Morphers\RotationalTextMorpher;

$pipeline = new CompositeTextMorpher();
$pipeline->add(new ReverseTextMorpher());
$pipeline->add(new RotationalTextMorpher(13));

$morphed = $pipeline->morph('Hello');
$original = $pipeline->unmorph($morphed);
```

This approach allows you to implement completely custom transformations while preserving the same architecture and developer experience as the package's built-in morphers.

---

## Testing

Run unit tests:

```sh
composer test
```

---

## License

This package is licensed under the [MIT License](https://github.com/omaressaouaf/text-morph/blob/master/LICENSE).

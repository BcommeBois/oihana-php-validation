# oihana/php-validation — validation rules & helpers for PHP

![Language](https://img.shields.io/badge/language-English-blue)

`oihana/php-validation` is a PHP 8.4+ library providing a curated set of composable, strongly-typed validation **rules** and expressive rule **helpers**, built on top of [Somnambulist Validation](https://github.com/somnambulist-tech/validation).

![Oihana PHP Validation](https://raw.githubusercontent.com/BcommeBois/oihana-php-validation/main/assets/images/oihana-php-validation-logo-inline-512x160.png)

## Who this documentation is for

PHP developers who want to:

- validate values against ready-made, tested **rules** — comparison, ranges, ISO 8601 dates, geo coordinates, postal codes, i18n maps, and more;
- validate against application **models** — `ExistModelRule`, `UniqueModelRule` resolve services from a PSR-11 container;
- build validation **rule strings** fluently with helper functions — `between()`, `requiredIf()`, `digitsBetween()`, …;
- write their **own rules** by extending the provided abstract base classes.

## Three ways to use a rule

**1. Standalone** — call `check()` directly:

```php
use oihana\validations\rules\ISO8601DateRule;

$rule = new ISO8601DateRule();
$rule->check( '1990-05-01' ); // true
```

**2. Registered with the Somnambulist `Factory`** — give the rule a name and use it in a rule string:

```php
use Somnambulist\Components\Validation\Factory;
use oihana\validations\rules\I18nRule;

$factory = new Factory();
$factory->addRule( 'i18n', new I18nRule( [ 'fr', 'en' ] ) );

$validation = $factory->validate(
    [ 'title' => [ 'fr' => 'Bonjour', 'en' => 'Hello' ] ],
    [ 'title' => 'required|array|i18n' ]
);

$validation->passes(); // true
```

**3. With rule-string helpers** — compose the rule expression itself:

```php
use function oihana\validations\rules\helpers\between;
use function oihana\validations\rules\helpers\rules;

rules( 'required', between( 0, 120 ) ); // "required|between:0,120"
```

## Table of contents

### Getting started — [`getting-started/`](getting-started/)

- [Introduction](getting-started/introduction.md) — what the library does and the *oihana* philosophy.
- [Installation](getting-started/installation.md) — PHP 8.4+ requirement and `composer require`.
- [Dependencies](getting-started/dependencies.md) — the runtime packages and their role.

### Usage

- [Rules](rules.md) — how rules work, registering them, and the `Rules` constant reference.
- [Comparison & range rules](comparison.md) — `EqualRule`, `GreaterThanRule`, `LessThanRule`, `RangeRule`.
- [ISO 8601 rules](iso8601.md) — date, datetime, duration and combined rules.
- [Geo rules](geo.md) — `LatitudeRule`, `LongitudeRule`, `ElevationRule`.
- [i18n & postal rules](i18n.md) — `I18nRule`, `PostalCodeRule`.
- [Model-aware rules](models.md) — `ExistModelRule`, `UniqueModelRule` and the container.
- [Other rules](other-rules.md) — color, constants, instance-of, starts-with, auth and HTTP rules.
- [Helpers](helpers.md) — the autoloaded rule-string functions.
- [Custom rules](custom-rules.md) — write your own rule on the provided abstracts.

### Cross-cutting

- [Tests & coverage](testing.md) — run the PHPUnit suite and measure coverage.

## Source code

The library code lives under [`src/oihana/validations/`](../../src/oihana/validations/) — namespace `oihana\validations`.

## See also

- [Packagist `oihana/php-validation`](https://packagist.org/packages/oihana/php-validation) — the package page.
- [API reference (phpDocumentor)](https://bcommebois.github.io/oihana-php-validation) — class-level generated reference.

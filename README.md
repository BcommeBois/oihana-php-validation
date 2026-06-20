# Oihana PHP - Validation

![Oihana PHP Validation](https://raw.githubusercontent.com/BcommeBois/oihana-php-validation/main/assets/images/oihana-php-validation-logo-inline-512x160.png)

Composable validation rules and helpers for PHP 8.4+, built on [Somnambulist Validation](https://github.com/somnambulist-tech/validation).

[![Latest Version](https://img.shields.io/packagist/v/oihana/php-validation.svg?style=flat-square)](https://packagist.org/packages/oihana/php-validation)  
[![Total Downloads](https://img.shields.io/packagist/dt/oihana/php-validation.svg?style=flat-square)](https://packagist.org/packages/oihana/php-validation)  
[![License](https://img.shields.io/packagist/l/oihana/php-validation.svg?style=flat-square)](LICENSE)

## 📚 Documentation

User guides (FR + EN), with narrative explanations and examples:

| 🇬🇧 **[English documentation](wiki/en/README.md)**                                              | 🇫🇷 **[Documentation française](wiki/fr/README.md)**                                            |
|-------------------------------------------------------------------------------------------------|-------------------------------------------------------------------------------------------------|
| Getting started, rules, helpers, ISO 8601, geo & i18n, model-aware rules, custom rules, testing. | Démarrage, règles, helpers, ISO 8601, géo & i18n, règles liées aux modèles, règles sur mesure, tests. |

Auto-generated API reference (phpDocumentor):  
👉 https://bcommebois.github.io/oihana-php-validation

## 🧠 What is it?

This package extends [Somnambulist Validation](https://github.com/somnambulist-tech/validation)
with a curated set of reusable, strongly-typed **rules** and expressive **helper
functions**. Each rule is a small, focused, tested class you register with the
validation factory; each helper builds a rule expression string fluently.

```php
use Somnambulist\Components\Validation\Factory;
use oihana\validations\rules\ISO8601DateRule;

use function oihana\validations\rules\helpers\between;

$factory = new Factory();
$factory->addRule( 'iso8601_date', new ISO8601DateRule() );

$validation = $factory->validate
(
    [ 'birthDate' => '1990-05-01', 'age' => 34 ],
    [
        'birthDate' => 'required|iso8601_date',
        'age'       => 'required|' . between( 0, 120 ), // "between:0,120"
    ]
);

$validation->passes(); // true
```

## 🚀 Features

- 🔢 Comparison & range rules — `EqualRule`, `GreaterThanRule`, `LessThanRule`, `RangeRule`.
- 📅 ISO 8601 date, datetime & duration rules — `ISO8601DateRule`, `ISO8601DateTimeRule`, `ISO8601DurationRule`.
- 🌍 Geo & i18n rules — `LatitudeRule`, `LongitudeRule`, `ElevationRule`, `I18nRule`, `PostalCodeRule`.
- 🗄️ Model-aware rules — `ExistModelRule`, `UniqueModelRule`, `ConstantsRule`.
- 🔐 Auth rules — `EffectRule`, `JWTAlgorithmRule`; HTTP — `HttpMethodRule`.
- 🧰 Expressive rule helpers — `between()`, `requiredIf()`, `digitsBetween()`, `arrayMustHaveKeys()`, and more.
- 🧪 Full unit-test coverage ensuring reliability and maintainability.

💡 Designed to be lightweight, testable, and compatible with any PHP 8.4+ project.

## 📦 Installation

> **Requires [PHP 8.4+](https://php.net/releases/)**  

Install via [Composer](https://getcomposer.org):
```bash
composer require oihana/php-validation
```

## ✅ Tests & coverage

Run the full unit-test suite (PHPUnit, strict mode):
```bash
composer test
```

Run a single test case:
```bash
./vendor/bin/phpunit --filter ISO8601DateRuleTest
```

Measure coverage (requires Xdebug or PCOV):
```bash
composer coverage        # text + Clover + HTML under build/coverage/
composer coverage:md     # readable Markdown summary (build/coverage/COVERAGE.md)
```

The suite runs in **strict mode** and targets **100% line coverage**.

## 🧾 License

This project is licensed under the [Mozilla Public License 2.0 (MPL-2.0)](https://www.mozilla.org/en-US/MPL/2.0/).

## 👤 About the author

* Author : Marc ALCARAZ (aka eKameleon)
* Mail : marc@ooop.fr
* Website : http://www.ooop.fr

## 🛠️ Generate the Documentation

We use [phpDocumentor](https://phpdoc.org/) to generate the documentation into the ./docs folder.

### Usage
Run the command : 
```bash
composer doc
```

## 🔗 Related packages

- [oihana/php-models](https://github.com/BcommeBois/oihana-php-models) – document/PDO models used by the model-aware rules.
- [oihana/php-standards](https://github.com/BcommeBois/oihana-php-standards) – ISO standards (ISO 8601, ISO 3166) used by the date and postal rules.
- [oihana/php-enums](https://github.com/BcommeBois/oihana-php-enums) – a collection of strongly-typed constant enumerations for PHP.
- [oihana/php-core](https://github.com/BcommeBois/oihana-php-core) – core helpers and utilities used by this library.

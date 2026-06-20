# Installation

## Requirements

- **PHP 8.4 or higher.**
- **[Composer](https://getcomposer.org/).**

The library itself requires no special PHP extension. Transitive dependencies
may require common extensions, which ship with most PHP distributions. The
model-aware rules need a PSR-11 container (for example `php-di/php-di`).

## Install via Composer

```bash
composer require oihana/php-validation
```

## Autoloading

Classes are autoloaded via PSR-4 under the `oihana\validations\` namespace, and
the 33 rule-string helpers via composer `autoload.files`:

```json
{
    "autoload": {
        "psr-4": {
            "oihana\\validations\\": "src/oihana/validations"
        },
        "files": [
            "src/oihana/validations/rules/helpers/after.php",
            "src/oihana/validations/rules/helpers/between.php",
            "src/oihana/validations/rules/helpers/requiredIf.php"
        ]
    }
}
```

> The snippet above is abbreviated — the package wires **all 33** helper files.
> See [Helpers](../helpers.md) for the complete list.

Once installed, import the classes and helpers directly:

```php
use oihana\validations\rules\ISO8601DateRule;

use function oihana\validations\rules\helpers\between;
```

## Verify the installation

```php
require 'vendor/autoload.php';

use oihana\validations\rules\ColorRule;

$rule = new ColorRule();
var_dump( $rule->check( '#ff00ff' ) ); // bool(true)
```

## Next steps

- [Dependencies](dependencies.md)
- [Rules](../rules.md)

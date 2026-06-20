# Installation

## Prérequis

- **PHP 8.4 ou supérieur.**
- **[Composer](https://getcomposer.org/).**

La bibliothèque elle-même ne requiert aucune extension PHP particulière. Les
dépendances transitives peuvent nécessiter des extensions courantes, présentes
dans la plupart des distributions PHP. Les règles liées aux modèles requièrent
un conteneur PSR-11 (par exemple `php-di/php-di`).

## Installation via Composer

```bash
composer require oihana/php-validation
```

## Autochargement

Les classes sont autochargées en PSR-4 sous le namespace `oihana\validations\`,
et les 33 helpers de chaîne de règles via `autoload.files` de composer :

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

> L'extrait ci-dessus est abrégé — le paquet câble **les 33** fichiers helpers.
> Voir [Helpers](../helpers.md) pour la liste complète.

Une fois installé, importez directement les classes et helpers :

```php
use oihana\validations\rules\ISO8601DateRule;

use function oihana\validations\rules\helpers\between;
```

## Vérifier l'installation

```php
require 'vendor/autoload.php';

use oihana\validations\rules\ColorRule;

$rule = new ColorRule();
var_dump( $rule->check( '#ff00ff' ) ); // bool(true)
```

## Étapes suivantes

- [Dépendances](dependencies.md)
- [Règles](../rules.md)

# oihana/php-validation — règles & helpers de validation pour PHP

![Langue](https://img.shields.io/badge/langue-Français-blue)

`oihana/php-validation` est une bibliothèque PHP 8.4+ qui fournit un ensemble soigné de **règles** de validation composables et fortement typées, ainsi que des **helpers** expressifs pour construire des chaînes de règles, le tout bâti sur [Somnambulist Validation](https://github.com/somnambulist-tech/validation).

![Oihana PHP Validation](https://raw.githubusercontent.com/BcommeBois/oihana-php-validation/main/assets/images/oihana-php-validation-logo-inline-512x160.png)

## À qui s'adresse cette documentation

Aux développeurs PHP qui souhaitent :

- valider des valeurs avec des **règles** prêtes à l'emploi et testées — comparaison, intervalles, dates ISO 8601, coordonnées géographiques, codes postaux, dictionnaires i18n, etc. ;
- valider par rapport aux **modèles** de l'application — `ExistModelRule`, `UniqueModelRule` résolvent des services depuis un conteneur PSR-11 ;
- construire des **chaînes de règles** de façon fluide avec des fonctions helpers — `between()`, `requiredIf()`, `digitsBetween()`, … ;
- écrire leurs **propres règles** en étendant les classes de base abstraites fournies.

## Trois façons d'utiliser une règle

**1. En autonome** — appeler `check()` directement :

```php
use oihana\validations\rules\ISO8601DateRule;

$rule = new ISO8601DateRule();
$rule->check( '1990-05-01' ); // true
```

**2. Enregistrée dans la `Factory` Somnambulist** — nommer la règle et l'utiliser dans une chaîne de règles :

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

**3. Avec les helpers de chaîne de règles** — composer l'expression de règle elle-même :

```php
use function oihana\validations\rules\helpers\between;
use function oihana\validations\rules\helpers\rules;

rules( 'required', between( 0, 120 ) ); // "required|between:0,120"
```

## Table des matières

### Démarrage — [`getting-started/`](getting-started/)

- [Introduction](getting-started/introduction.md) — ce que fait la bibliothèque et la philosophie *oihana*.
- [Installation](getting-started/installation.md) — prérequis PHP 8.4+ et `composer require`.
- [Dépendances](getting-started/dependencies.md) — les paquets runtime et leur rôle.

### Utilisation

- [Règles](rules.md) — fonctionnement des règles, enregistrement et référence des constantes `Rules`.
- [Règles de comparaison & d'intervalle](comparison.md) — `EqualRule`, `GreaterThanRule`, `LessThanRule`, `RangeRule`.
- [Règles ISO 8601](iso8601.md) — date, datetime, durée et règles combinées.
- [Règles géographiques](geo.md) — `LatitudeRule`, `LongitudeRule`, `ElevationRule`.
- [Règles i18n & postales](i18n.md) — `I18nRule`, `PostalCodeRule`.
- [Règles liées aux modèles](models.md) — `ExistModelRule`, `UniqueModelRule` et le conteneur.
- [Autres règles](other-rules.md) — couleur, constantes, instance-of, starts-with, règles auth et HTTP.
- [Helpers](helpers.md) — les fonctions de chaîne de règles autochargées.
- [Règles sur mesure](custom-rules.md) — écrire sa propre règle sur les abstractions fournies.

### Transverse

- [Tests & couverture](testing.md) — lancer la suite PHPUnit et mesurer la couverture.

## Code source

Le code de la bibliothèque se trouve sous [`src/oihana/validations/`](../../src/oihana/validations/) — namespace `oihana\validations`.

## Voir aussi

- [Packagist `oihana/php-validation`](https://packagist.org/packages/oihana/php-validation) — la page du paquet.
- [Référence API (phpDocumentor)](https://bcommebois.github.io/oihana-php-validation) — référence générée au niveau des classes.

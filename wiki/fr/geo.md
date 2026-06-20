# Règles géographiques

Les règles géographiques valident des coordonnées : **latitude**, **longitude** et **élévation** (altitude). Chacune vérifie que la valeur est numérique et qu'elle se situe dans la plage valide de sa dimension.

Ces trois règles étendent [`AbstractRangeRule`](custom-rules.md), la classe de base des contrôles de plage numérique. Elle définit une borne inférieure inclusive `$min` et une borne supérieure `$max`, ainsi qu'une méthode `check()` qui :

- rejette `null` et la chaîne vide `''` ;
- convertit la valeur avec `oihana\core\toNumber()` et rejette tout ce qui n'est pas numérique (une chaîne non numérique renvoie `false`) ;
- renvoie `true` uniquement lorsque `$min <= valeur <= $max`.

Comme la conversion accepte les chaînes numériques, des valeurs telles que `'89.99'` ou `'120.5'` sont valides. Les bornes sont également lisibles à l'exécution via `getMin()` et `getMax()`.

| Règle | Classe | Plage (inclusive) |
|---|---|---|
| Latitude | `oihana\validations\rules\geo\LatitudeRule` | `-90` … `90` |
| Longitude | `oihana\validations\rules\geo\LongitudeRule` | `-180` … `180` |
| Élévation | `oihana\validations\rules\geo\ElevationRule` | `-11500` … `8900` (mètres) |

## LatitudeRule

`oihana\validations\rules\geo\LatitudeRule` valide une latitude géographique, en degrés, entre **-90 et 90** (inclus).

Le constructeur ne prend aucun argument. Le message d'erreur par défaut est :

```
The :attribute must be a valid latitude between -90 and 90 degrees.
```

**Utilisation autonome**

```php
use oihana\validations\rules\geo\LatitudeRule;

$rule = new LatitudeRule();

$rule->check( 0 );        // true
$rule->check( 45 );       // true
$rule->check( -90 );      // true  (borne inférieure)
$rule->check( 90 );       // true  (borne supérieure)
$rule->check( '89.99' );  // true  (chaîne numérique)
$rule->check( 90.0001 );  // false (hors plage)
$rule->check( -91 );      // false
$rule->check( 'foo' );    // false (non numérique)
$rule->check( null );     // false

$rule->getMin(); // -90
$rule->getMax(); // 90
```

**Enregistrement avec la `Factory` Somnambulist**

```php
use Somnambulist\Components\Validation\Factory;
use oihana\validations\rules\geo\LatitudeRule;

$factory = new Factory();
$factory->addRule( 'latitude', new LatitudeRule() );

$validation = $factory->validate(
    [ 'lat' => 48.8566 ],
    [ 'lat' => 'required|latitude' ]
);

$validation->passes(); // true
```

## LongitudeRule

`oihana\validations\rules\geo\LongitudeRule` valide une longitude géographique, en degrés, entre **-180 et 180** (inclus).

Le constructeur ne prend aucun argument. Le message d'erreur par défaut est :

```
The :attribute must be a valid longitude between -180 and 180 degrees.
```

**Utilisation autonome**

```php
use oihana\validations\rules\geo\LongitudeRule;

$rule = new LongitudeRule();

$rule->check( 100 );      // true
$rule->check( -100 );     // true
$rule->check( 180 );      // true  (borne supérieure)
$rule->check( '120.5' );  // true  (chaîne numérique)
$rule->check( 190 );      // false (hors plage)
$rule->check( -181 );     // false
$rule->check( 'foo' );    // false (non numérique)
$rule->check( null );     // false

$rule->getMin(); // -180
$rule->getMax(); // 180
```

**Enregistrement avec la `Factory` Somnambulist**

```php
use Somnambulist\Components\Validation\Factory;
use oihana\validations\rules\geo\LongitudeRule;

$factory = new Factory();
$factory->addRule( 'longitude', new LongitudeRule() );

$validation = $factory->validate(
    [ 'lng' => 2.3522 ],
    [ 'lng' => 'required|longitude' ]
);

$validation->passes(); // true
```

## ElevationRule

`oihana\validations\rules\geo\ElevationRule` valide une élévation (altitude) **en mètres**, entre **-11500** (sous la plus grande profondeur océanique) et **8900** (au-dessus du mont Everest), inclus.

Le constructeur ne prend aucun argument. Le message d'erreur par défaut est :

```
The :attribute must be a valid elevation between -11500 and 8900 meters.
```

**Utilisation autonome**

```php
use oihana\validations\rules\geo\ElevationRule;

$rule = new ElevationRule();

$rule->check( 0 );        // true  (niveau de la mer)
$rule->check( 8848 );     // true  (mont Everest)
$rule->check( -10994 );   // true  (fosse des Mariannes)
$rule->check( 8900 );     // true  (borne supérieure)
$rule->check( -11500 );   // true  (borne inférieure)
$rule->check( '500' );    // true  (chaîne numérique)
$rule->check( 9000 );     // false (trop haut)
$rule->check( -12000 );   // false (trop profond)
$rule->check( 'foo' );    // false (non numérique)
$rule->check( null );     // false

$rule->getMin(); // -11500
$rule->getMax(); // 8900
```

**Enregistrement avec la `Factory` Somnambulist**

```php
use Somnambulist\Components\Validation\Factory;
use oihana\validations\rules\geo\ElevationRule;

$factory = new Factory();
$factory->addRule( 'elevation', new ElevationRule() );

$validation = $factory->validate(
    [ 'altitude' => 1633 ],
    [ 'altitude' => 'required|elevation' ]
);

$validation->passes(); // true
```

## Voir aussi

- [Rules](rules.md) — fonctionnement des règles et référence des constantes `Rules`.
- [Comparison & range rules](comparison.md) — `EqualRule`, `GreaterThanRule`, `LessThanRule`, `RangeRule`.
- [Custom rules](custom-rules.md) — étendre `AbstractRangeRule` et les autres classes abstraites.
- [Index de la documentation](README.md) — retour à la table des matières.

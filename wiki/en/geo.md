# Geo rules

The geo rules validate geographic coordinates: **latitude**, **longitude** and **elevation** (altitude). Each one ensures that a value is numeric and falls within the valid range for its dimension.

All three rules extend [`AbstractRangeRule`](custom-rules.md), the base class for numeric range checks. It defines an inclusive lower bound `$min` and upper bound `$max`, and a `check()` method that:

- rejects `null` and the empty string `''`;
- converts the value with `oihana\core\toNumber()` and rejects anything that is not numeric (a non-numeric string returns `false`);
- returns `true` only when `$min <= value <= $max`.

Because the conversion accepts numeric strings, values such as `'89.99'` or `'120.5'` are valid. The bounds are also readable at runtime through `getMin()` and `getMax()`.

| Rule | Class | Range (inclusive) |
|---|---|---|
| Latitude | `oihana\validations\rules\geo\LatitudeRule` | `-90` … `90` |
| Longitude | `oihana\validations\rules\geo\LongitudeRule` | `-180` … `180` |
| Elevation | `oihana\validations\rules\geo\ElevationRule` | `-11500` … `8900` (meters) |

## LatitudeRule

`oihana\validations\rules\geo\LatitudeRule` validates a geographic latitude, in degrees, between **-90 and 90** (inclusive).

The constructor takes no arguments. The default error message is:

```
The :attribute must be a valid latitude between -90 and 90 degrees.
```

**Standalone**

```php
use oihana\validations\rules\geo\LatitudeRule;

$rule = new LatitudeRule();

$rule->check( 0 );        // true
$rule->check( 45 );       // true
$rule->check( -90 );      // true  (lower bound)
$rule->check( 90 );       // true  (upper bound)
$rule->check( '89.99' );  // true  (numeric string)
$rule->check( 90.0001 );  // false (out of range)
$rule->check( -91 );      // false
$rule->check( 'foo' );    // false (not numeric)
$rule->check( null );     // false

$rule->getMin(); // -90
$rule->getMax(); // 90
```

**Registered with the Somnambulist `Factory`**

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

`oihana\validations\rules\geo\LongitudeRule` validates a geographic longitude, in degrees, between **-180 and 180** (inclusive).

The constructor takes no arguments. The default error message is:

```
The :attribute must be a valid longitude between -180 and 180 degrees.
```

**Standalone**

```php
use oihana\validations\rules\geo\LongitudeRule;

$rule = new LongitudeRule();

$rule->check( 100 );      // true
$rule->check( -100 );     // true
$rule->check( 180 );      // true  (upper bound)
$rule->check( '120.5' );  // true  (numeric string)
$rule->check( 190 );      // false (out of range)
$rule->check( -181 );     // false
$rule->check( 'foo' );    // false (not numeric)
$rule->check( null );     // false

$rule->getMin(); // -180
$rule->getMax(); // 180
```

**Registered with the Somnambulist `Factory`**

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

`oihana\validations\rules\geo\ElevationRule` validates an elevation (altitude) **in meters**, between **-11500** (below the deepest ocean depth) and **8900** (above Mount Everest), inclusive.

The constructor takes no arguments. The default error message is:

```
The :attribute must be a valid elevation between -11500 and 8900 meters.
```

**Standalone**

```php
use oihana\validations\rules\geo\ElevationRule;

$rule = new ElevationRule();

$rule->check( 0 );        // true  (sea level)
$rule->check( 8848 );     // true  (Mount Everest)
$rule->check( -10994 );   // true  (Mariana Trench)
$rule->check( 8900 );     // true  (upper bound)
$rule->check( -11500 );   // true  (lower bound)
$rule->check( '500' );    // true  (numeric string)
$rule->check( 9000 );     // false (too high)
$rule->check( -12000 );   // false (too deep)
$rule->check( 'foo' );    // false (not numeric)
$rule->check( null );     // false

$rule->getMin(); // -11500
$rule->getMax(); // 8900
```

**Registered with the Somnambulist `Factory`**

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

## See also

- [Rules](rules.md) — how rules work and the `Rules` constant reference.
- [Comparison & range rules](comparison.md) — `EqualRule`, `GreaterThanRule`, `LessThanRule`, `RangeRule`.
- [Custom rules](custom-rules.md) — extend `AbstractRangeRule` and the other abstracts.
- [Documentation index](README.md) — back to the table of contents.

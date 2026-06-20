# Comparison & range rules

These rules compare a **numeric value** against either another field in the validated data set or a fixed numeric constant. They are useful for enforcing relationships between fields (durations, limits, quotas, password lengths) or for validating a value against a hard threshold.

All comparison rules extend [`ComparisonRule`](#base-classes), which exposes a single parameter named `comparison_field` (the constant `ComparisonRule::COMPARISON_FIELD`). The parameter is *dual-purpose*:

- if the value passed to the rule is **numeric** (e.g. `gt_field:600`), it is treated as a fixed literal;
- otherwise it is treated as the **name of another field** and resolved from the validated data (e.g. `gt_field:requiredPasswordLength`).

Before comparing, both operands are coerced to numbers via `oihana\core\toNumber()`. This means integers, floats, numeric strings (`'600'`) and scientific notation (`'2e2'`) are all accepted. If either operand is `null`, or cannot be coerced to a number, the comparison fails (returns `false`).

The `RangeRule` (and its base [`AbstractRangeRule`](#base-classes)) follows the same numeric coercion approach but checks that a value lies within a closed `[min, max]` interval rather than comparing two operands.

> Each rule is registered with a name of your choosing on the Somnambulist `Factory`. The names used below (`eq_field`, `gt_field`, …) are the conventional ones taken from the test suite; you are free to pick others.

## EqualRule

Validates that a value is **equal to** the comparison field or constant (`$a == $b`).

| | |
|---|---|
| Class | `oihana\validations\rules\EqualRule` |
| Base class | `ComparisonRule` |
| Parameter | `comparison_field` (another field name, or a numeric literal) |
| Default message | `The :attribute must equal to :comparison_field.` |

```php
use oihana\validations\rules\EqualRule;
use Somnambulist\Components\Validation\Factory;

$validator = new Factory();
$validator->addRule( 'eq_field', new EqualRule() );

// Compare against another field
$validation = $validator->validate(
    [
        'minimumPasswordLength'  => 8,
        'requiredPasswordLength' => 8,
    ],
    [
        'minimumPasswordLength' => 'required|integer|eq_field:requiredPasswordLength',
    ]
);

$validation->passes(); // true

// Compare against a fixed numeric constant
$validation = $validator->validate(
    [ 'timeout' => 3600 ],
    [ 'timeout' => 'required|integer|eq_field:3600' ]
);

$validation->passes(); // true
```

## GreaterThanRule

Validates that a value is **strictly greater than** the comparison field or constant (`$a > $b`).

| | |
|---|---|
| Class | `oihana\validations\rules\GreaterThanRule` |
| Base class | `ComparisonRule` |
| Parameter | `comparison_field` (another field name, or a numeric literal) |
| Default message | `The :attribute must be greater than :comparison_field.` |

```php
use oihana\validations\rules\GreaterThanRule;
use Somnambulist\Components\Validation\Factory;

$validator = new Factory();
$validator->addRule( 'gt_field', new GreaterThanRule() );

// Greater than another field
$validation = $validator->validate(
    [
        'minimumPasswordLength'  => 12,
        'requiredPasswordLength' => 8,
    ],
    [
        'minimumPasswordLength' => 'required|integer|gt_field:requiredPasswordLength',
    ]
);

$validation->passes(); // true

// Greater than a fixed value (equal is not enough)
$validation = $validator->validate(
    [ 'timeout' => 600 ],
    [ 'timeout' => 'required|integer|gt_field:600' ]
);

$validation->fails(); // true — 600 is not > 600
```

## GreaterThanOrEqualRule

Validates that a value is **greater than or equal to** the comparison field or constant (`$a >= $b`).

| | |
|---|---|
| Class | `oihana\validations\rules\GreaterThanOrEqualRule` |
| Base class | `ComparisonRule` |
| Parameter | `comparison_field` (another field name, or a numeric literal) |
| Default message | `The :attribute must be greater than or equal to :comparison_field.` |

```php
use oihana\validations\rules\GreaterThanOrEqualRule;
use Somnambulist\Components\Validation\Factory;

$validator = new Factory();
$validator->addRule( 'gte_field', new GreaterThanOrEqualRule() );

$validation = $validator->validate(
    [
        'implicitHybridTokenLifetime'   => 3600,
        'maximumAccessTokenExpiration'  => 3600,
    ],
    [
        'implicitHybridTokenLifetime' => 'required|integer|gte_field:maximumAccessTokenExpiration',
    ]
);

$validation->passes(); // true — equal is allowed
```

## LessThanRule

Validates that a value is **strictly less than** the comparison field or constant (`$a < $b`).

| | |
|---|---|
| Class | `oihana\validations\rules\LessThanRule` |
| Base class | `ComparisonRule` |
| Parameter | `comparison_field` (another field name, or a numeric literal) |
| Default message | `The :attribute must be less than :comparison_field.` |

```php
use oihana\validations\rules\LessThanRule;
use Somnambulist\Components\Validation\Factory;

$validator = new Factory();
$validator->addRule( 'lt_field', new LessThanRule() );

// Less than a fixed value
$validation = $validator->validate(
    [ 'timeout' => 300 ],
    [ 'timeout' => 'required|integer|lt_field:600' ]
);

$validation->passes(); // true
```

## LessThanOrEqualRule

Validates that a value is **less than or equal to** the comparison field or constant (`$a <= $b`).

| | |
|---|---|
| Class | `oihana\validations\rules\LessThanOrEqualRule` |
| Base class | `ComparisonRule` |
| Parameter | `comparison_field` (another field name, or a numeric literal) |
| Default message | `The :attribute must be less than or equal to :comparison_field.` |

```php
use oihana\validations\rules\LessThanOrEqualRule;
use Somnambulist\Components\Validation\Factory;

$validator = new Factory();
$validator->addRule( 'lte_field', new LessThanOrEqualRule() );

$validation = $validator->validate(
    [
        'implicitHybridTokenLifetime'   => 3600,
        'maximumAccessTokenExpiration'  => 3600,
    ],
    [
        'implicitHybridTokenLifetime' => 'required|integer|lte_field:maximumAccessTokenExpiration',
    ]
);

$validation->passes(); // true — equal is allowed
```

## RangeRule

Validates that a numeric value lies within a closed `[min, max]` interval (both bounds **inclusive**). Unlike the comparison rules, `RangeRule` takes **two** parameters, `min` and `max` (constants `RangeRule::MIN` and `RangeRule::MAX`), and does not reference another field.

| | |
|---|---|
| Class | `oihana\validations\rules\RangeRule` |
| Base class | `AbstractRangeRule` |
| Parameters | `min`, `max` (numeric; numeric strings are accepted) |
| Default message | `The :attribute must be between :min and :max.` |

Both bounds are coerced with `toNumber()`. If `min` or `max` is missing, or cannot be coerced to a number, a `Somnambulist\Components\Validation\Exceptions\ParameterException` is thrown.

Standalone usage — fill the parameters explicitly, then call `check()`:

```php
use oihana\validations\rules\RangeRule;

$rule = new RangeRule();
$rule->fillParameters( [ 'min' => 0, 'max' => 100 ] );

$rule->check( 50 );  // true
$rule->check( 0 );   // true — lower bound is inclusive
$rule->check( 100 ); // true — upper bound is inclusive
$rule->check( 101 ); // false
$rule->check( 'foo' ); // false — not numeric
```

Registered with the `Factory`, the bounds are given in the rule string as `range:min,max`:

```php
use oihana\validations\rules\RangeRule;
use Somnambulist\Components\Validation\Factory;

$validator = new Factory();
$validator->addRule( 'range', new RangeRule() );

$validation = $validator->validate(
    [ 'score' => 50 ],
    [ 'score' => 'required|range:0,100' ]
);

$validation->passes(); // true
```

## Base classes

If you want to build your own comparison or range rule, extend one of the two abstract base classes rather than reimplementing the numeric coercion logic.

### `ComparisonRule`

`oihana\validations\rules\abstracts\ComparisonRule` extends the Somnambulist `Rule`. It declares the `comparison_field` parameter, performs the `toNumber()` coercion of both operands, short-circuits to `false` on `null` or non-numeric values, and delegates the actual decision to an abstract method you implement:

```php
abstract protected function compare( float|int $a , float|int $b ) : bool;
```

`$a` is the attribute value, `$b` is the resolved comparison value. `EqualRule`, `GreaterThanRule`, `GreaterThanOrEqualRule`, `LessThanRule` and `LessThanOrEqualRule` each implement only this one method.

### `AbstractRangeRule`

`oihana\validations\rules\abstracts\AbstractRangeRule` also extends `Rule`. Subclasses set the protected `float|int $min` and `float|int $max` bounds; its `check()` coerces the value with `toNumber()` and returns `true` when `$min <= value <= $max`. It exposes `getMin()` and `getMax()` accessors. `RangeRule` builds on it by reading the bounds from the `min`/`max` rule parameters.

See [Custom rules](custom-rules.md) for a full walkthrough of writing and registering a rule on top of these abstracts.

## See also

- [Rules](rules.md) — how rules work, registering them, and the rule-name reference.
- [ISO 8601 rules](iso8601.md) — date, datetime, duration and combined rules.
- [Custom rules](custom-rules.md) — write your own rule on the provided abstracts.
- [Documentation index](README.md) — back to the documentation home.

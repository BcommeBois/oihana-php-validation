# Helpers

The `oihana\validations\rules\helpers` namespace provides **33 free functions** (registered through composer `autoload.files`) that build Somnambulist/Laravel-style rule-string fragments. Each function returns a small string such as `between(0,120)` → `"between:0,120"`, which you then compose into a full validation expression.

They are global functions, not class methods. Import each one with a `use function` statement:

```php
use function oihana\validations\rules\helpers\between;
use function oihana\validations\rules\helpers\min;
use function oihana\validations\rules\helpers\rules;
```

Most validation libraries (Somnambulist Validation, Laravel) separate multiple constraints on a field with the **pipe** character (`|`), e.g. `'required|min:5|max:10'`. Two helpers exist to combine the fragments produced by the others:

- **`rule()`** builds a single `name[:value,...]` fragment from a name and optional values.
- **`rules()`** joins several fragments (strings or arrays) with the pipe (`|`).

Using these helpers instead of raw strings keeps your rule definitions type-checked, refactorable, and free of magic strings.

## Combine rules

| Function | Returns (example) | Meaning |
|---|---|---|
| `rule( string $name , mixed ...$values )` | `rule('my_rule')` → `"my_rule"`<br>`rule('my_rule', 5)` → `"my_rule:5"`<br>`rule('my_rule', 5, 'hello')` → `"my_rule:5,hello"` | Build a single `name[:value1,value2,...]` rule fragment. |
| `rules( string\|array ...$rules )` | `rules('required', min(5), max(10))` → `"required\|min:5\|max:10"`<br>`rules(['required', 'min:5', 'max:10'])` → `"required\|min:5\|max:10"` | Concatenate several rule fragments (strings or arrays) with the pipe (`\|`). |

## Presence & requirement

| Function | Returns (example) | Meaning |
|---|---|---|
| `requires( string ...$fields )` | `requires('email', 'password')` → `"requires:email,password"` | The listed fields must be present and non-empty (fails with `sometimes`/`nullable`). |
| `requiredIf( string $anotherField , mixed ...$values )` | `requiredIf('name', 'foo', 'bar')` → `"required_if:name,foo,bar"` | Required when `anotherField` equals any of the values. |
| `requiredUnless( string $anotherField , mixed ...$values )` | `requiredUnless('name', 'foo', 'bar')` → `"required_unless:name,foo,bar"` | Required unless `anotherField` equals one of the values. |
| `requiredWith( string ...$fields )` | `requiredWith('email', 'password')` → `"required_with:email,password"` | Required when **any** of the listed fields are present. |
| `requiredWithAll( string ...$fields )` | `requiredWithAll('email', 'password')` → `"required_with_all:email,password"` | Required when **all** of the listed fields are present. |
| `requiredWithout( string ...$fields )` | `requiredWithout('email', 'password')` → `"required_without:email,password"` | Required when **any** of the listed fields are absent. |
| `requiredWithoutAll( string ...$fields )` | `requiredWithoutAll('email', 'password')` → `"required_without_all:email,password"` | Required when **all** of the listed fields are absent. |
| `prohibitedIf( string $anotherField , mixed ...$values )` | `prohibitedIf('password', 'foo', 'bar')` → `"prohibited_if:password,foo,bar"` | Not allowed when `anotherField` provides any of the values. |
| `prohibitedUnless( string $anotherField , mixed ...$values )` | `prohibitedUnless('password', 'foo', 'bar')` → `"prohibited_unless:password,foo,bar"` | Not allowed unless `anotherField` has one of the values. |
| `defaultValue( mixed $value )` | `defaultValue(1)` → `"default:1"` | Use this default in the validated data when the attribute has no value. |

## Size & numeric

| Function | Returns (example) | Meaning |
|---|---|---|
| `between( string\|int\|float $min , string\|int\|float $max )` | `between(10, 20)` → `"between:10,20"`<br>`between('1M', '2M')` → `"between:1M,2M"` | Size must be between `min` and `max` (also works on uploaded file sizes). |
| `min( string\|int\|float $value )` | `min(2)` → `"min:2"`<br>`min(-90)` → `"min:-90"`<br>`min('1M')` → `"min:1M"` | Size greater than or equal to the given value. |
| `max( string\|int\|float $value )` | `max(10)` → `"max:10"`<br>`max('2M')` → `"max:2M"` | Size less than or equal to the given value. |
| `length( string\|int $value )` | `length(10)` → `"length:10"` | String of exactly the given length. |
| `digits( int $value )` | `digits(4)` → `"digits:4"` | Numeric value with an exact length of `value` digits. |
| `digitsBetween( int $min , int $max )` | `digitsBetween(2, 5)` → `"digits_between:2,5"` | Numeric value with a length between `min` and `max`. |

## String

| Function | Returns (example) | Meaning |
|---|---|---|
| `startsWith( string $anotherField )` | `startsWith('prefix')` → `"starts_with:prefix"` | The value must start with `anotherField`. |
| `endsWith( string $anotherField )` | `endsWith('suffix')` → `"ends_with:suffix"` | The value must end with `anotherField`. |
| `regex( string $regex )` | `regex('/(this\|that\|value)/')` → `"regex:/(this\|that\|value)/"` | The value must match the given regular expression. |
| `url( null\|array\|string $scheme = null )` | `url()` → `"url"`<br>`url('http')` → `"url:http"`<br>`url('http,https')` → `"url:http,https"`<br>`url(['http','https'])` → `"url:http,https"`<br>`url('ftp')` → `"url:ftp"` | Valid URL format, optionally restricted to the given scheme(s). |

## Set membership & equality

| Function | Returns (example) | Meaning |
|---|---|---|
| `in( string ...$values )` | `in('foo', 'bar')` → `"in:foo,bar"` | The value must be included in the given list. |
| `notIn( string ...$values )` | `notIn('foo', 'bar')` → `"not_in:foo,bar"` | The value must not be included in the given list. |
| `same( string $anotherField )` | `same('password')` → `"same:password"` | The value must equal the value of `anotherField`. |
| `different( string $anotherField )` | `different('name')` → `"different:name"` | The value must differ from the value of `anotherField`. |

## Files

| Function | Returns (example) | Meaning |
|---|---|---|
| `extension( string ...$values )` | `extension('jpg', 'png')` → `"extension:jpg,png"` | The path/URL must end with one of the listed extensions (use for paths/URLs). |
| `mimes( string ...$values )` | `mimes('jpg', 'png')` → `"mimes:jpg,png"` | The uploaded `$_FILES` item must match one of the listed extensions' MIME types. |

## Dates

| Function | Returns (example) | Meaning |
|---|---|---|
| `after( string $date )` | `after('2016-12-31')` → `"after:2016-12-31"` | The date must be after the given date (anything `strtotime` can parse). |
| `before( string $date )` | `before('2016-12-31')` → `"before:2016-12-31"` | The date must be before the given date (anything `strtotime` can parse). |
| `date( ?string $format = null )` | `date()` → `"date"`<br>`date('Y-m-d')` → `"date:Y-m-d"` | Valid date following the given format (default `Y-m-d`). |

## Arrays

| Function | Returns (example) | Meaning |
|---|---|---|
| `arrayMustHaveKeys( string ...$values )` | `arrayMustHaveKeys('foo', 'bar')` → `"array_must_have_keys:foo,bar"` | The array must contain all the listed keys (extra keys allowed). |
| `arrayCanOnlyHaveKeys( string ...$values )` | `arrayCanOnlyHaveKeys('foo', 'bar')` → `"array_can_only_have_keys:foo,bar"` | The array may only contain the listed keys; any other key fails. |

## A combined example

Helpers shine when composed with `rules()` to build complete, readable rule strings:

```php
use function oihana\validations\rules\helpers\between;
use function oihana\validations\rules\helpers\in;
use function oihana\validations\rules\helpers\min;
use function oihana\validations\rules\helpers\rules;

use Somnambulist\Components\Validation\Factory;

$factory = new Factory();

$validation = $factory->validate
(
    [
        'name'   => 'Jane',
        'age'    => 34,
        'status' => 'active',
    ],
    [
        'name'   => rules( 'required', min(2) ),            // 'required|min:2'
        'age'    => rules( 'required', between(0, 120) ),   // 'required|between:0,120'
        'status' => rules( 'required', in('active', 'inactive') ), // 'required|in:active,inactive'
    ]
);

$validation->passes(); // true
```

## See also

- [Rules](rules.md)
- [Custom rules](custom-rules.md)
- Back to the [Documentation index](README.md)

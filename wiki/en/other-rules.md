# Other rules

A grab-bag of general-purpose and domain-specific rules: hex color expressions, membership in a class's constants, instance-of checks, string-prefix checks, and the authentication / HTTP rules built on top of `ConstantsRule`.

All of these extend the Somnambulist `Rule` base class, so they can be used three ways: standalone via `check()`, registered on a `Factory` under a name, or wrapped inside a `Validator`.

## ColorRule

Validates that a value is a color expression. By default it matches a 6-digit hexadecimal code prefixed with `#` (the pattern `/^%s[a-fA-F0-9]{6}$/`, where `%s` is replaced by the prefix). Non-string values always fail.

**Constructor** — `new ColorRule( array $init = [] )`, where `$init` accepts two optional keys:

- `prefix` (`ColorRule::PREFIX`) — the prefix character(s); defaults to `#`.
- `pattern` (`ColorRule::PATTERN`) — a regex format string; the `%s` placeholder receives the (regex-quoted) prefix. Defaults to `ColorRule::DEFAULT_PATTERN`.

Both can also be set fluently with `->prefix()` and `->pattern()`.

```php
use oihana\validations\rules\ColorRule;

$rule = new ColorRule();
$rule->check( '#ff00ff' ); // true
$rule->check( '#A1B2C3' ); // true
$rule->check( 'ff00ff' );  // false (missing '#')
$rule->check( '#GGGGGG' ); // false
$rule->check( 123456 );    // false (not a string)

// No '#' prefix:
$rule = new ColorRule( [ 'prefix' => '' ] );
$rule->check( 'ff00ff' );  // true
$rule->check( '#ff00ff' ); // false

// Uppercase-only pattern:
$rule = new ColorRule( [ 'pattern' => '/^%s[A-F0-9]{6}$/', 'prefix' => '#' ] );
$rule->check( '#ABCDEF' ); // true
$rule->check( '#abc123' ); // false
```

## ConstantsRule

Validates that a value is one of the constants exposed by a class that uses `oihana\reflect\traits\ConstantsTrait`. This is the generic base for the auth and HTTP rules below.

**Constructor** — `new ConstantsRule( string $className, ?array $cases = null )`:

- `$className` — the fully qualified class name. It **must** use `ConstantsTrait`, otherwise an `InvalidArgumentException` is thrown.
- `$cases` — an optional subset of allowed values. When omitted (or empty), it defaults to every value returned by `$className::enums()`.

Comparison is strict (`in_array( …, true )`), so values are type- and case-sensitive. The allowed list and class name can be reconfigured fluently with `->cases()` and `->className()`.

```php
use oihana\validations\rules\ConstantsRule;
use Somnambulist\Components\Validation\Validator;

// Given a class using ConstantsTrait, e.g.:
//   final class Status { use ConstantsTrait;
//       public const string ACTIVE   = 'active';
//       public const string ARCHIVED = 'archived';
//   }

// All constants of the class:
$rule = new ConstantsRule( Status::class );
$rule->check( 'active' );  // true
$rule->check( 'invalid' ); // false

// Restrict to a subset:
$rule = new ConstantsRule( Status::class, [ 'active' ] );
$rule->check( 'archived' ); // false

// Inside a Validator with a custom message:
$rule = new ConstantsRule( Status::class );
$rule->message( ':attribute must be a valid status.' );

$validator = new Validator(
    [ 'status' => 'active' ],
    [ 'status' => [ $rule ] ]
);
$validator->passes(); // true
```

## InstanceOfRule

Validates that a value is an instance of a given class. The check fails when the value is not an object, when no class name is set, or when the configured class does not exist.

**Constructor** — `new InstanceOfRule( ?string $className = null )`. The class name can also be set fluently with `->className()`.

```php
use oihana\validations\rules\InstanceOfRule;
use DateTime;

$rule = new InstanceOfRule( DateTime::class );
$rule->check( new DateTime() );  // true
$rule->check( new stdClass() );  // false
$rule->check( 'string' );        // false (not an object)

// Unknown class name => always false:
$rule = new InstanceOfRule( 'NonExistingClass' );
$rule->check( new stdClass() );  // false
```

## StartsWithRule

Validates that a string value begins with a given prefix. The rule passes when the value starts with the prefix, when the value equals the prefix, or when the prefix is empty.

**Constructor** — `new StartsWithRule( ?string $prefix = null )`. The prefix can also be set fluently with `->prefix()`.

```php
use oihana\validations\rules\StartsWithRule;

$rule = new StartsWithRule( 'abc' );
$rule->check( 'abcdef' ); // true (starts with prefix)
$rule->check( 'xyzabc' ); // false

$rule = new StartsWithRule( 'hello' );
$rule->check( 'hello' );  // true (equals prefix)

$rule = new StartsWithRule( '' );
$rule->check( 'anything' ); // true (empty prefix)
```

A `Stringable` object is accepted: its string form is tested against the prefix.

## EffectRule

An authorization-effect rule for Casbin / RBAC permissions. It extends `ConstantsRule` and is bound to `xyz\oihana\schema\constants\Effect`, whose only valid values are:

```
allow, deny
```

**Constructor** — `new EffectRule()` (no arguments). Comparison is case-sensitive, so `'ALLOW'` is rejected.

```php
use oihana\validations\rules\auth\EffectRule;
use Somnambulist\Components\Validation\Validator;

$rule = new EffectRule();
$rule->check( 'allow' );   // true
$rule->check( 'deny' );    // true
$rule->check( 'unknown' ); // false
$rule->check( 'ALLOW' );   // false (case-sensitive)

$validator = new Validator(
    [ 'effect' => 'deny' ],
    [ 'effect' => [ $rule ] ]
);
$validator->passes(); // true
// On failure: "effect is not a valid. Allowed values are 'allow' or 'deny'."
```

## JWTAlgorithmRule

Validates that a value is a supported JSON Web Token signing algorithm. It extends `ConstantsRule` and is bound to `xyz\oihana\schema\constants\JWTAlgorithm`. The full set of allowed values is:

```
HS256, HS384, HS512, RS256, RS384, RS512, PS256, PS384, PS512, none
```

**Constructor** — `new JWTAlgorithmRule( ?array $cases = null )`. Pass `$cases` to restrict the rule to a subset. Comparison is case-sensitive (`'hs256'` is rejected).

```php
use oihana\validations\rules\auth\JWTAlgorithmRule;
use Somnambulist\Components\Validation\Validator;

$rule = new JWTAlgorithmRule();
$rule->check( 'HS256' ); // true
$rule->check( 'MD5' );   // false
$rule->check( 'hs256' ); // false (case-sensitive)

// Restrict to a subset:
$rule = new JWTAlgorithmRule( [ 'HS256', 'RS256' ] );
$rule->check( 'HS256' ); // true
$rule->check( 'RS512' ); // false

$validator = new Validator(
    [ 'alg' => 'HS256' ],
    [ 'alg' => [ new JWTAlgorithmRule() ] ]
);
$validator->passes(); // true
// On failure: "alg is not a valid JWT signing algorithm."
```

## HttpMethodRule

Validates that a value is a supported HTTP method. It extends `ConstantsRule` and is bound to `oihana\enums\http\HttpMethod`, which exposes the standard verbs:

```
GET, POST, PUT, PATCH, DELETE, HEAD, OPTIONS, TRACE, CONNECT, PURGE
```

**Constructor** — `new HttpMethodRule( ?array $cases = null )`. Pass `$cases` to restrict the rule to a subset. Comparison is strict against the enum values.

```php
use oihana\validations\rules\http\HttpMethodRule;
use Somnambulist\Components\Validation\Validator;

$rule = new HttpMethodRule();
$rule->check( 'GET' );  // true
$rule->check( 'FOO' );  // false
$rule->check( 'gEt' );  // false

// Restrict to a subset:
$rule = new HttpMethodRule( [ 'GET', 'POST', 'DELETE' ] );
$rule->check( 'POST' );  // true
$rule->check( 'PATCH' ); // false

$validator = new Validator(
    [ 'method' => 'GET' ],
    [ 'method' => [ new HttpMethodRule() ] ]
);
$validator->passes(); // true
// On failure: "method is not a valid HTTP method."
```

## See also

- [Rules](rules.md) — how rules work, registering them, and the `Rules` constant reference.
- [Helpers](helpers.md) — the autoloaded rule-string functions.
- [Custom rules](custom-rules.md) — write your own rule on the provided abstracts.
- Back to the [Documentation index](README.md).

# Custom rules

`oihana/php-validation` is built on top of [Somnambulist Validation](https://github.com/somnambulist-tech/validation), so writing your own rule is straightforward. You have two starting points:

- extend Somnambulist's `Somnambulist\Components\Validation\Rule` **directly** — full control, minimal boilerplate;
- extend one of this library's three **abstract base classes**, each of which factors out a recurring concern:
  - `ComparisonRule` — compare a numeric value against another field or a fixed number;
  - `AbstractRangeRule` — ensure a numeric value lies within a `[min, max]` range;
  - `ContainerRule` — rules that need a PSR-11 container (and get logging for free).

Whichever base you pick, the contract is the same: implement the validation logic and expose a `protected string $message` used when validation fails.

## Extending `Rule` directly

A rule is a class extending `Rule` that declares a `$message` template and a `check( mixed $value ): bool` method. Optionally, it can declare `$fillableParams` — the ordered list of parameter names that can be filled positionally from a rule string.

The bundled `ColorRule` is a faithful example: it validates a value against a configurable regex pattern.

```php
<?php

use Somnambulist\Components\Validation\Exceptions\ParameterException;
use Somnambulist\Components\Validation\Rule;

class UppercaseRule extends Rule
{
    /**
     * The error message used when validation fails.
     */
    protected string $message = ':attribute must be uppercase.';

    /**
     * Optional: parameters that can be filled positionally from a rule string.
     */
    protected array $fillableParams = [];

    /**
     * Returns true when the value is valid.
     */
    public function check( mixed $value ): bool
    {
        if ( !is_string( $value ) )
        {
            return false ;
        }
        return $value === strtoupper( $value ) ;
    }
}
```

Inside `check()`, the `:attribute` placeholder of `$message` is replaced with the field name. When your rule defines parameters, you read them with `$this->parameter( $name )` and can assert their presence with `$this->assertHasRequiredParameters( [ ... ] )` (which throws a `ParameterException` when one is missing) — exactly as `ColorRule` does.

Once the rule exists, register it with the Somnambulist `Factory` to use it in a rule string:

```php
<?php

use Somnambulist\Components\Validation\Factory;

$factory = new Factory();
$factory->addRule( 'uppercase', new UppercaseRule() );

$validation = $factory->validate(
    [ 'code' => 'ABC' ],
    [ 'code' => 'required|uppercase' ]
);

$validation->passes(); // true
```

## Extending `ComparisonRule`

`oihana\validations\rules\abstracts\ComparisonRule` factors out the logic for comparing a numeric attribute against either another field's value or a fixed numeric constant. It already implements `check()`, which:

- reads the `comparison_field` parameter (exposed as `ComparisonRule::COMPARISON_FIELD`);
- resolves it to a value — a literal number if numeric, otherwise the value of the named sibling field;
- converts both operands to numbers and returns `false` on `null` / non-numeric input;
- delegates the final decision to your `compare()` implementation.

A subclass therefore only has to implement one abstract method:

```php
abstract protected function compare( float|int $a , float|int $b ) : bool ;
```

Here `$a` is the attribute value and `$b` is the comparison value. The bundled `GreaterThanRule` is a one-liner:

```php
<?php

use oihana\validations\rules\abstracts\ComparisonRule;

class GreaterThanRule extends ComparisonRule
{
    protected string $message = 'The :attribute must be greater than :comparison_field.';

    protected function compare( float|int $a , float|int $b ): bool
    {
        return $a > $b ;
    }
}
```

Used in a rule string, the parameter after the colon becomes `comparison_field` — either a sibling field name or a literal number:

```php
// compare against another field
[ 'end' => 'gte_field:start' ]

// compare against a fixed value
[ 'timeout' => 'gte_field:3600' ]
```

## Extending `AbstractRangeRule`

`oihana\validations\rules\abstracts\AbstractRangeRule` validates that a numeric value lies within an inclusive `[min, max]` range. The base class already implements `check()`, which rejects `null` / empty / non-numeric input and then returns `true` when the value is `>= $min` and `<= $max`.

Subclasses do not override `check()`; they simply define the two bounds as properties:

```php
protected float|int $min ;
protected float|int $max ;
```

The base class also ships a default message template, `'The :attribute must be between :min and :max.'`, and two accessors, `getMin()` and `getMax()`. A concrete latitude rule is just a matter of fixing the bounds:

```php
<?php

use oihana\validations\rules\abstracts\AbstractRangeRule;

class LatitudeRule extends AbstractRangeRule
{
    protected float|int $min = -90 ;
    protected float|int $max =  90 ;

    protected string $message = 'The :attribute must be a valid latitude (between :min and :max).' ;
}
```

## Extending `ContainerRule`

`oihana\validations\rules\abstracts\ContainerRule` is the base for rules that need access to a PSR-11 dependency injection container — typically to resolve a service while validating. Its constructor is:

```php
public function __construct( ContainerInterface $container , array $init = [] )
```

- `$container` — the PSR-11 `Psr\Container\ContainerInterface` instance, stored on the protected `$container` property;
- `$init` — an options array forwarded to the logger initialization.

`ContainerRule` composes `LoggerTrait` and `ToStringTrait`, and calls `initializeLogger( $init , $container )` in its constructor, so subclasses get logging out of the box (resolve a logger by passing a `logger` id or instance in `$init`).

This is the foundation for the model-aware rules. See [Model-aware rules](models.md) for `ExistModelRule` and `UniqueModelRule`, the real-world rules that extend `ContainerRule` to resolve a model service from the container and validate a value against your application data.

## See also

- [Rules](rules.md) — how rules work, registering them, and the `Rules` constant reference.
- [Comparison & range rules](comparison.md) — the concrete comparison and range rules.
- [Model-aware rules](models.md) — `ExistModelRule`, `UniqueModelRule` and the container.
- [Documentation index](README.md) — back to the table of contents.

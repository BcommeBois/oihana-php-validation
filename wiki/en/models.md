# Model-aware rules

![Language](https://img.shields.io/badge/language-English-blue)

Model-aware rules validate a value against an application **model** — a service that knows how to look documents up. Instead of holding their own data, these rules resolve the model from a [PSR-11](https://www.php-fig.org/psr/psr-11/) container and delegate the lookup to it.

Two rules are provided:

- [`ExistModelRule`](#existmodelrule) — passes when a matching document **exists** in the model.
- [`UniqueModelRule`](#uniquemodelrule) — passes when the value is **unique** (no matching document exists).

Both extend [`ContainerRule`](#containerrule), whose constructor takes a `Psr\Container\ContainerInterface` plus an `$init` options array. The model itself is identified by an entry id stored in the options (`ExistModelRule::MODEL`); at check time the rule resolves that id from the container with `$container->get( ... )`. The model must implement `oihana\models\interfaces\ExistModel`, i.e. expose an `exist( array $criteria ): bool` method.

The examples below use `MockDocumentsModel` from the test suite — an in-memory model that implements `ExistModel` — so they are runnable as-is.

## ContainerRule

`oihana\validations\rules\abstracts\ContainerRule` is the abstract base for every rule that needs dependency injection. It extends the Somnambulist `Rule` class and provides:

- a **container reference** — a `Psr\Container\ContainerInterface` stored as `$this->container`, from which models (and any other service) are resolved;
- a **logger** — through `oihana\logging\LoggerTrait`, initialized from `$init` and the container in the constructor;
- a **string representation** — through `oihana\traits\ToStringTrait`.

```php
public function __construct( ContainerInterface $container , array $init = [] )
```

You normally don't instantiate `ContainerRule` directly — you extend it (as `ExistModelRule` does) or use one of the concrete rules below.

## ExistModelRule

`oihana\validations\rules\models\ExistModelRule` passes when a document matching the checked value **exists** in the resolved model.

### Options

The rule reads the following keys from `$init`:

| Constant | Key | Description |
|----------|-----|-------------|
| `ExistModelRule::MODEL` | `'model'` | The container entry id of the model to resolve. |
| `ExistModelRule::KEY` | `'key'` | The document property to match the value against. Defaults to `ExistModelRule::DEFAULT_KEY` (`Schema::ID`, i.e. `'id'`). |

`check( $value )` resolves the model from the container, and — if it is an `ExistModel` — returns `$model->exist( [ ModelParam::KEY => $key, ModelParam::VALUE => $value ] )`. If the model id is not a string, is missing from the container, or the resolved entry is not an `ExistModel`, `check()` returns `false`.

### Example — match on the default key

```php
use DI\Container;
use oihana\validations\rules\models\ExistModelRule;
use tests\oihana\models\mocks\MockDocumentsModel;

$model = new MockDocumentsModel();
$model->addDocument( [ 'id' => 1 , 'name' => 'John' ] );

$container = new Container();
$container->set( 'model' , $model );

$rule = new ExistModelRule( $container , [ ExistModelRule::MODEL => 'model' ] );

$rule->check( 1 );       // true  — a document with id 1 exists
$rule->check( 'hello' ); // false — no such id
```

### Example — custom key

Pass `ExistModelRule::KEY` to match the value against another property:

```php
$rule = new ExistModelRule
(
    $container ,
    [
        ExistModelRule::MODEL => 'model' ,
        ExistModelRule::KEY   => 'name'  ,
    ]
);

$rule->check( 'John' );  // true
$rule->check( 'hello' ); // false
```

### Example — string-init shortcut defines the model

When the second argument is a string, it is taken as the model id directly; an optional third argument sets the key:

```php
$rule = new ExistModelRule( $container , 'model' , 'name' );

$rule->check( 'John' );  // true
$rule->check( 'hello' ); // false
```

## UniqueModelRule

`oihana\validations\rules\models\UniqueModelRule` extends `ExistModelRule` and **inverts** its logic: `check()` returns the negation of the parent, so the rule passes only when the value does **not** already exist in the model. It accepts the same options (`MODEL`, `KEY`) and the same string-init shortcut.

Two behaviours worth noting:

- An **empty value** (`''` or `null`) is treated as unique — the underlying model returns no match for an empty lookup, so `check()` returns `true`.
- A **missing model** throws `Somnambulist\Components\Validation\Exceptions\ParameterException`: the inherited `check()` asserts that the required `MODEL` (and `KEY`) parameters are present before resolving anything.

### Example — uniqueness check

```php
use DI\Container;
use oihana\validations\rules\models\ExistModelRule;
use oihana\validations\rules\models\UniqueModelRule;
use tests\oihana\models\mocks\MockDocumentsModel;

$model = new MockDocumentsModel();
$model->addDocument( [ 'id' => 1 , 'email' => 'john@example.com' ] );

$container = new Container();
$container->set( 'user.model' , $model );

$rule = new UniqueModelRule
(
    $container ,
    [
        ExistModelRule::MODEL => 'user.model' ,
        ExistModelRule::KEY   => 'email'      ,
    ]
);

$rule->check( 'unique@example.com' ); // true  — not yet used
$rule->check( 'john@example.com' );   // false — already exists
$rule->check( '' );                   // true  — empty value treated as unique
$rule->check( null );                 // true
```

### Example — missing model throws

```php
use DI\Container;
use oihana\validations\rules\models\UniqueModelRule;
use Somnambulist\Components\Validation\Exceptions\ParameterException;

$rule = new UniqueModelRule( new Container() , [] );

$rule->check( 'anything' ); // throws ParameterException — no MODEL provided
```

## See also

- [Rules](rules.md) — how rules work and the `Rules` constant reference.
- [Custom rules](custom-rules.md) — write your own rule on the provided abstracts.
- [Documentation index](README.md) — back to the table of contents.
- [oihana/php-models](https://github.com/BcommeBois/oihana-php-models) — the models library that defines the `ExistModel` interface and `ModelParam` keys used here.

# Autres règles

Un ensemble de règles à usage général et spécifiques à un domaine : expressions de couleur hexadécimale, appartenance aux constantes d'une classe, vérifications d'instance, vérifications de préfixe de chaîne, ainsi que les règles d'authentification et HTTP construites au-dessus de `ConstantsRule`.

Toutes ces règles étendent la classe de base `Rule` de Somnambulist : elles s'utilisent donc de trois façons : de manière autonome via `check()`, enregistrées sur une `Factory` sous un nom, ou encapsulées dans un `Validator`.

## ColorRule

Valide qu'une valeur est une expression de couleur. Par défaut, elle correspond à un code hexadécimal à 6 chiffres préfixé par `#` (le motif `/^%s[a-fA-F0-9]{6}$/`, où `%s` est remplacé par le préfixe). Les valeurs non textuelles échouent toujours.

**Constructeur** — `new ColorRule( array $init = [] )`, où `$init` accepte deux clés optionnelles :

- `prefix` (`ColorRule::PREFIX`) — le ou les caractères de préfixe ; vaut `#` par défaut.
- `pattern` (`ColorRule::PATTERN`) — une chaîne de format regex ; le marqueur `%s` reçoit le préfixe (échappé pour la regex). Vaut `ColorRule::DEFAULT_PATTERN` par défaut.

Les deux peuvent aussi être définis de manière fluide avec `->prefix()` et `->pattern()`.

```php
use oihana\validations\rules\ColorRule;

$rule = new ColorRule();
$rule->check( '#ff00ff' ); // true
$rule->check( '#A1B2C3' ); // true
$rule->check( 'ff00ff' );  // false (préfixe '#' manquant)
$rule->check( '#GGGGGG' ); // false
$rule->check( 123456 );    // false (pas une chaîne)

// Sans préfixe '#' :
$rule = new ColorRule( [ 'prefix' => '' ] );
$rule->check( 'ff00ff' );  // true
$rule->check( '#ff00ff' ); // false

// Motif en majuscules uniquement :
$rule = new ColorRule( [ 'pattern' => '/^%s[A-F0-9]{6}$/', 'prefix' => '#' ] );
$rule->check( '#ABCDEF' ); // true
$rule->check( '#abc123' ); // false
```

## ConstantsRule

Valide qu'une valeur est l'une des constantes exposées par une classe utilisant `oihana\reflect\traits\ConstantsTrait`. Il s'agit de la base générique des règles d'authentification et HTTP ci-dessous.

**Constructeur** — `new ConstantsRule( string $className, ?array $cases = null )` :

- `$className` — le nom de classe pleinement qualifié. Il **doit** utiliser `ConstantsTrait`, sinon une `InvalidArgumentException` est levée.
- `$cases` — un sous-ensemble optionnel de valeurs autorisées. S'il est omis (ou vide), il correspond par défaut à toutes les valeurs renvoyées par `$className::enums()`.

La comparaison est stricte (`in_array( …, true )`) : les valeurs sont donc sensibles au type et à la casse. La liste autorisée et le nom de classe peuvent être reconfigurés de manière fluide avec `->cases()` et `->className()`.

```php
use oihana\validations\rules\ConstantsRule;
use Somnambulist\Components\Validation\Validator;

// Avec une classe utilisant ConstantsTrait, par exemple :
//   final class Status { use ConstantsTrait;
//       public const string ACTIVE   = 'active';
//       public const string ARCHIVED = 'archived';
//   }

// Toutes les constantes de la classe :
$rule = new ConstantsRule( Status::class );
$rule->check( 'active' );  // true
$rule->check( 'invalid' ); // false

// Restreindre à un sous-ensemble :
$rule = new ConstantsRule( Status::class, [ 'active' ] );
$rule->check( 'archived' ); // false

// Dans un Validator avec un message personnalisé :
$rule = new ConstantsRule( Status::class );
$rule->message( ':attribute must be a valid status.' );

$validator = new Validator(
    [ 'status' => 'active' ],
    [ 'status' => [ $rule ] ]
);
$validator->passes(); // true
```

## InstanceOfRule

Valide qu'une valeur est une instance d'une classe donnée. La vérification échoue lorsque la valeur n'est pas un objet, lorsqu'aucun nom de classe n'est défini, ou lorsque la classe configurée n'existe pas.

**Constructeur** — `new InstanceOfRule( ?string $className = null )`. Le nom de classe peut aussi être défini de manière fluide avec `->className()`.

```php
use oihana\validations\rules\InstanceOfRule;
use DateTime;

$rule = new InstanceOfRule( DateTime::class );
$rule->check( new DateTime() );  // true
$rule->check( new stdClass() );  // false
$rule->check( 'string' );        // false (pas un objet)

// Nom de classe inconnu => toujours false :
$rule = new InstanceOfRule( 'NonExistingClass' );
$rule->check( new stdClass() );  // false
```

## StartsWithRule

Valide qu'une valeur textuelle commence par un préfixe donné. La règle passe lorsque la valeur commence par le préfixe, lorsque la valeur est égale au préfixe, ou lorsque le préfixe est vide.

**Constructeur** — `new StartsWithRule( ?string $prefix = null )`. Le préfixe peut aussi être défini de manière fluide avec `->prefix()`.

```php
use oihana\validations\rules\StartsWithRule;

$rule = new StartsWithRule( 'abc' );
$rule->check( 'abcdef' ); // true (commence par le préfixe)
$rule->check( 'xyzabc' ); // false

$rule = new StartsWithRule( 'hello' );
$rule->check( 'hello' );  // true (égale le préfixe)

$rule = new StartsWithRule( '' );
$rule->check( 'anything' ); // true (préfixe vide)
```

Un objet `Stringable` est accepté : sa forme textuelle est comparée au préfixe.

## EffectRule

Une règle d'effet d'autorisation pour les permissions Casbin / RBAC. Elle étend `ConstantsRule` et est liée à `xyz\oihana\schema\constants\Effect`, dont les seules valeurs valides sont :

```
allow, deny
```

**Constructeur** — `new EffectRule()` (sans argument). La comparaison est sensible à la casse, `'ALLOW'` est donc rejeté.

```php
use oihana\validations\rules\auth\EffectRule;
use Somnambulist\Components\Validation\Validator;

$rule = new EffectRule();
$rule->check( 'allow' );   // true
$rule->check( 'deny' );    // true
$rule->check( 'unknown' ); // false
$rule->check( 'ALLOW' );   // false (sensible à la casse)

$validator = new Validator(
    [ 'effect' => 'deny' ],
    [ 'effect' => [ $rule ] ]
);
$validator->passes(); // true
// En cas d'échec : "effect is not a valid. Allowed values are 'allow' or 'deny'."
```

## JWTAlgorithmRule

Valide qu'une valeur est un algorithme de signature JSON Web Token pris en charge. Elle étend `ConstantsRule` et est liée à `xyz\oihana\schema\constants\JWTAlgorithm`. L'ensemble complet des valeurs autorisées est :

```
HS256, HS384, HS512, RS256, RS384, RS512, PS256, PS384, PS512, none
```

**Constructeur** — `new JWTAlgorithmRule( ?array $cases = null )`. Passez `$cases` pour restreindre la règle à un sous-ensemble. La comparaison est sensible à la casse (`'hs256'` est rejeté).

```php
use oihana\validations\rules\auth\JWTAlgorithmRule;
use Somnambulist\Components\Validation\Validator;

$rule = new JWTAlgorithmRule();
$rule->check( 'HS256' ); // true
$rule->check( 'MD5' );   // false
$rule->check( 'hs256' ); // false (sensible à la casse)

// Restreindre à un sous-ensemble :
$rule = new JWTAlgorithmRule( [ 'HS256', 'RS256' ] );
$rule->check( 'HS256' ); // true
$rule->check( 'RS512' ); // false

$validator = new Validator(
    [ 'alg' => 'HS256' ],
    [ 'alg' => [ new JWTAlgorithmRule() ] ]
);
$validator->passes(); // true
// En cas d'échec : "alg is not a valid JWT signing algorithm."
```

## HttpMethodRule

Valide qu'une valeur est une méthode HTTP prise en charge. Elle étend `ConstantsRule` et est liée à `oihana\enums\http\HttpMethod`, qui expose les verbes standard :

```
GET, POST, PUT, PATCH, DELETE, HEAD, OPTIONS, TRACE, CONNECT, PURGE
```

**Constructeur** — `new HttpMethodRule( ?array $cases = null )`. Passez `$cases` pour restreindre la règle à un sous-ensemble. La comparaison est stricte par rapport aux valeurs de l'énumération.

```php
use oihana\validations\rules\http\HttpMethodRule;
use Somnambulist\Components\Validation\Validator;

$rule = new HttpMethodRule();
$rule->check( 'GET' );  // true
$rule->check( 'FOO' );  // false
$rule->check( 'gEt' );  // false

// Restreindre à un sous-ensemble :
$rule = new HttpMethodRule( [ 'GET', 'POST', 'DELETE' ] );
$rule->check( 'POST' );  // true
$rule->check( 'PATCH' ); // false

$validator = new Validator(
    [ 'method' => 'GET' ],
    [ 'method' => [ new HttpMethodRule() ] ]
);
$validator->passes(); // true
// En cas d'échec : "method is not a valid HTTP method."
```

## Voir aussi

- [Rules](rules.md) — fonctionnement des règles, leur enregistrement et la référence des constantes `Rules`.
- [Helpers](helpers.md) — les fonctions de chaîne de règles chargées automatiquement.
- [Custom rules](custom-rules.md) — écrire votre propre règle sur les classes abstraites fournies.
- Retour à l'[index de la documentation](README.md).

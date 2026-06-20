# Règles sur mesure

`oihana/php-validation` est construit au-dessus de [Somnambulist Validation](https://github.com/somnambulist-tech/validation), ce qui rend l'écriture de vos propres règles très simple. Vous disposez de deux points de départ :

- étendre **directement** le `Somnambulist\Components\Validation\Rule` de Somnambulist — contrôle total, code minimal ;
- étendre l'une des trois **classes de base abstraites** de cette bibliothèque, chacune factorisant une préoccupation récurrente :
  - `ComparisonRule` — comparer une valeur numérique à un autre champ ou à un nombre fixe ;
  - `AbstractRangeRule` — garantir qu'une valeur numérique se situe dans un intervalle `[min, max]` ;
  - `ContainerRule` — règles nécessitant un conteneur PSR-11 (et qui bénéficient de la journalisation gratuitement).

Quelle que soit la base choisie, le contrat est identique : implémenter la logique de validation et exposer une propriété `protected string $message` utilisée lorsque la validation échoue.

## Étendre `Rule` directement

Une règle est une classe étendant `Rule` qui déclare un modèle `$message` et une méthode `check( mixed $value ): bool`. Optionnellement, elle peut déclarer `$fillableParams` — la liste ordonnée des noms de paramètres remplissables par position depuis une chaîne de règle.

La classe `ColorRule` fournie est un exemple fidèle : elle valide une valeur contre un motif d'expression régulière configurable.

```php
<?php

use Somnambulist\Components\Validation\Exceptions\ParameterException;
use Somnambulist\Components\Validation\Rule;

class UppercaseRule extends Rule
{
    /**
     * Le message d'erreur utilisé lorsque la validation échoue.
     */
    protected string $message = ':attribute must be uppercase.';

    /**
     * Optionnel : les paramètres remplissables par position depuis une chaîne de règle.
     */
    protected array $fillableParams = [];

    /**
     * Retourne true lorsque la valeur est valide.
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

Dans `check()`, le marqueur `:attribute` de `$message` est remplacé par le nom du champ. Lorsque votre règle définit des paramètres, vous les lisez avec `$this->parameter( $name )` et pouvez en vérifier la présence avec `$this->assertHasRequiredParameters( [ ... ] )` (qui lève une `ParameterException` lorsqu'un paramètre manque) — exactement comme le fait `ColorRule`.

Une fois la règle créée, enregistrez-la auprès de la `Factory` de Somnambulist pour l'utiliser dans une chaîne de règle :

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

## Étendre `ComparisonRule`

`oihana\validations\rules\abstracts\ComparisonRule` factorise la logique de comparaison d'un attribut numérique avec soit la valeur d'un autre champ, soit une constante numérique fixe. Elle implémente déjà `check()`, qui :

- lit le paramètre `comparison_field` (exposé via `ComparisonRule::COMPARISON_FIELD`) ;
- le résout en une valeur — un nombre littéral s'il est numérique, sinon la valeur du champ voisin nommé ;
- convertit les deux opérandes en nombres et retourne `false` sur une entrée `null` / non numérique ;
- délègue la décision finale à votre implémentation de `compare()`.

Une sous-classe n'a donc qu'à implémenter une seule méthode abstraite :

```php
abstract protected function compare( float|int $a , float|int $b ) : bool ;
```

Ici `$a` est la valeur de l'attribut et `$b` la valeur de comparaison. La classe `GreaterThanRule` fournie tient en une ligne :

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

Utilisé dans une chaîne de règle, le paramètre après les deux-points devient `comparison_field` — soit le nom d'un champ voisin, soit un nombre littéral :

```php
// comparaison avec un autre champ
[ 'end' => 'gte_field:start' ]

// comparaison avec une valeur fixe
[ 'timeout' => 'gte_field:3600' ]
```

## Étendre `AbstractRangeRule`

`oihana\validations\rules\abstracts\AbstractRangeRule` valide qu'une valeur numérique se situe dans un intervalle inclusif `[min, max]`. La classe de base implémente déjà `check()`, qui rejette les entrées `null` / vides / non numériques puis retourne `true` lorsque la valeur est `>= $min` et `<= $max`.

Les sous-classes ne redéfinissent pas `check()` ; elles définissent simplement les deux bornes comme propriétés :

```php
protected float|int $min ;
protected float|int $max ;
```

La classe de base fournit également un modèle de message par défaut, `'The :attribute must be between :min and :max.'`, ainsi que deux accesseurs, `getMin()` et `getMax()`. Une règle de latitude concrète se résume à fixer les bornes :

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

## Étendre `ContainerRule`

`oihana\validations\rules\abstracts\ContainerRule` est la base des règles nécessitant l'accès à un conteneur d'injection de dépendances PSR-11 — typiquement pour résoudre un service pendant la validation. Son constructeur est :

```php
public function __construct( ContainerInterface $container , array $init = [] )
```

- `$container` — l'instance PSR-11 `Psr\Container\ContainerInterface`, stockée dans la propriété protégée `$container` ;
- `$init` — un tableau d'options transmis à l'initialisation du logger.

`ContainerRule` compose `LoggerTrait` et `ToStringTrait`, et appelle `initializeLogger( $init , $container )` dans son constructeur, de sorte que les sous-classes bénéficient de la journalisation d'emblée (résolvez un logger en passant un identifiant ou une instance `logger` dans `$init`).

C'est le socle des règles liées aux modèles. Voir [Model-aware rules](models.md) pour `ExistModelRule` et `UniqueModelRule`, les règles concrètes qui étendent `ContainerRule` afin de résoudre un service de modèle depuis le conteneur et de valider une valeur contre les données de votre application.

## Voir aussi

- [Rules](rules.md) — fonctionnement des règles, leur enregistrement et la référence des constantes `Rules`.
- [Comparison & range rules](comparison.md) — les règles concrètes de comparaison et d'intervalle.
- [Model-aware rules](models.md) — `ExistModelRule`, `UniqueModelRule` et le conteneur.
- [Documentation index](README.md) — retour à la table des matières.

# Règles liées aux modèles

![Langue](https://img.shields.io/badge/langue-Français-blue)

Les règles orientées modèle valident une valeur par rapport à un **modèle** applicatif — un service qui sait rechercher des documents. Plutôt que de détenir leurs propres données, ces règles résolvent le modèle depuis un conteneur [PSR-11](https://www.php-fig.org/psr/psr-11/) et lui délèguent la recherche.

Deux règles sont fournies :

- [`ExistModelRule`](#existmodelrule) — réussit lorsqu'un document correspondant **existe** dans le modèle.
- [`UniqueModelRule`](#uniquemodelrule) — réussit lorsque la valeur est **unique** (aucun document correspondant n'existe).

Les deux étendent [`ContainerRule`](#containerrule), dont le constructeur prend un `Psr\Container\ContainerInterface` ainsi qu'un tableau d'options `$init`. Le modèle lui-même est identifié par un identifiant d'entrée stocké dans les options (`ExistModelRule::MODEL`) ; au moment de la vérification, la règle résout cet identifiant depuis le conteneur via `$container->get( ... )`. Le modèle doit implémenter `oihana\models\interfaces\ExistModel`, c'est-à-dire exposer une méthode `exist( array $criteria ): bool`.

Les exemples ci-dessous utilisent `MockDocumentsModel`, issu de la suite de tests — un modèle en mémoire qui implémente `ExistModel` — afin de pouvoir être exécutés tels quels.

## ContainerRule

`oihana\validations\rules\abstracts\ContainerRule` est la classe de base abstraite de toute règle nécessitant l'injection de dépendances. Elle étend la classe `Rule` de Somnambulist et fournit :

- une **référence au conteneur** — un `Psr\Container\ContainerInterface` stocké dans `$this->container`, à partir duquel les modèles (et tout autre service) sont résolus ;
- un **logger** — via `oihana\logging\LoggerTrait`, initialisé à partir de `$init` et du conteneur dans le constructeur ;
- une **représentation sous forme de chaîne** — via `oihana\traits\ToStringTrait`.

```php
public function __construct( ContainerInterface $container , array $init = [] )
```

En général, on n'instancie pas `ContainerRule` directement : on l'étend (comme le fait `ExistModelRule`) ou on utilise l'une des règles concrètes ci-dessous.

## ExistModelRule

`oihana\validations\rules\models\ExistModelRule` réussit lorsqu'un document correspondant à la valeur vérifiée **existe** dans le modèle résolu.

### Options

La règle lit les clés suivantes dans `$init` :

| Constante | Clé | Description |
|-----------|-----|-------------|
| `ExistModelRule::MODEL` | `'model'` | L'identifiant d'entrée du modèle à résoudre dans le conteneur. |
| `ExistModelRule::KEY` | `'key'` | La propriété du document à laquelle comparer la valeur. Par défaut `ExistModelRule::DEFAULT_KEY` (`Schema::ID`, soit `'id'`). |

`check( $value )` résout le modèle depuis le conteneur et — s'il s'agit d'un `ExistModel` — retourne `$model->exist( [ ModelParam::KEY => $key, ModelParam::VALUE => $value ] )`. Si l'identifiant du modèle n'est pas une chaîne, est absent du conteneur, ou si l'entrée résolue n'est pas un `ExistModel`, `check()` retourne `false`.

### Exemple — correspondance sur la clé par défaut

```php
use DI\Container;
use oihana\validations\rules\models\ExistModelRule;
use tests\oihana\models\mocks\MockDocumentsModel;

$model = new MockDocumentsModel();
$model->addDocument( [ 'id' => 1 , 'name' => 'John' ] );

$container = new Container();
$container->set( 'model' , $model );

$rule = new ExistModelRule( $container , [ ExistModelRule::MODEL => 'model' ] );

$rule->check( 1 );       // true  — un document avec l'id 1 existe
$rule->check( 'hello' ); // false — aucun id de ce type
```

### Exemple — clé personnalisée

Passez `ExistModelRule::KEY` pour comparer la valeur à une autre propriété :

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

### Exemple — le raccourci d'initialisation par chaîne définit le modèle

Lorsque le deuxième argument est une chaîne, il est interprété directement comme l'identifiant du modèle ; un troisième argument facultatif définit la clé :

```php
$rule = new ExistModelRule( $container , 'model' , 'name' );

$rule->check( 'John' );  // true
$rule->check( 'hello' ); // false
```

## UniqueModelRule

`oihana\validations\rules\models\UniqueModelRule` étend `ExistModelRule` et **inverse** sa logique : `check()` retourne la négation du parent, de sorte que la règle ne réussit que lorsque la valeur n'existe **pas** déjà dans le modèle. Elle accepte les mêmes options (`MODEL`, `KEY`) et le même raccourci d'initialisation par chaîne.

Deux comportements à noter :

- Une **valeur vide** (`''` ou `null`) est considérée comme unique — le modèle sous-jacent ne retourne aucune correspondance pour une recherche vide, donc `check()` retourne `true`.
- Un **modèle manquant** lève `Somnambulist\Components\Validation\Exceptions\ParameterException` : le `check()` hérité vérifie que les paramètres requis `MODEL` (et `KEY`) sont présents avant de résoudre quoi que ce soit.

### Exemple — vérification d'unicité

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

$rule->check( 'unique@example.com' ); // true  — pas encore utilisée
$rule->check( 'john@example.com' );   // false — existe déjà
$rule->check( '' );                   // true  — valeur vide considérée comme unique
$rule->check( null );                 // true
```

### Exemple — un modèle manquant lève une exception

```php
use DI\Container;
use oihana\validations\rules\models\UniqueModelRule;
use Somnambulist\Components\Validation\Exceptions\ParameterException;

$rule = new UniqueModelRule( new Container() , [] );

$rule->check( 'anything' ); // lève ParameterException — aucun MODEL fourni
```

## Voir aussi

- [Rules](rules.md) — comment fonctionnent les règles et la référence des constantes `Rules`.
- [Custom rules](custom-rules.md) — écrivez votre propre règle à partir des classes abstraites fournies.
- [Index de la documentation](README.md) — retour à la table des matières.
- [oihana/php-models](https://github.com/BcommeBois/oihana-php-models) — la bibliothèque de modèles qui définit l'interface `ExistModel` et les clés `ModelParam` utilisées ici.

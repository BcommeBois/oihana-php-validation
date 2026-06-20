# Règles de comparaison & d'intervalle

Ces règles comparent une **valeur numérique** soit à un autre champ de l'ensemble de données validé, soit à une constante numérique fixe. Elles sont utiles pour imposer des relations entre champs (durées, limites, quotas, longueurs de mot de passe) ou pour valider une valeur par rapport à un seuil fixe.

Toutes les règles de comparaison étendent [`ComparisonRule`](#classes-de-base), qui expose un unique paramètre nommé `comparison_field` (la constante `ComparisonRule::COMPARISON_FIELD`). Ce paramètre a un *double usage* :

- si la valeur passée à la règle est **numérique** (par ex. `gt_field:600`), elle est traitée comme un littéral fixe ;
- sinon elle est traitée comme le **nom d'un autre champ** et résolue à partir des données validées (par ex. `gt_field:requiredPasswordLength`).

Avant la comparaison, les deux opérandes sont converties en nombres via `oihana\core\toNumber()`. Ainsi, les entiers, les flottants, les chaînes numériques (`'600'`) et la notation scientifique (`'2e2'`) sont tous acceptés. Si l'une des opérandes vaut `null`, ou ne peut pas être convertie en nombre, la comparaison échoue (retourne `false`).

La règle `RangeRule` (et sa classe de base [`AbstractRangeRule`](#classes-de-base)) suit la même approche de conversion numérique, mais vérifie qu'une valeur se situe dans un intervalle fermé `[min, max]` au lieu de comparer deux opérandes.

> Chaque règle est enregistrée sous un nom de votre choix auprès de la `Factory` Somnambulist. Les noms utilisés ci-dessous (`eq_field`, `gt_field`, …) sont les noms conventionnels issus de la suite de tests ; vous êtes libre d'en choisir d'autres.

## EqualRule

Valide qu'une valeur est **égale** au champ ou à la constante de comparaison (`$a == $b`).

| | |
|---|---|
| Classe | `oihana\validations\rules\EqualRule` |
| Classe de base | `ComparisonRule` |
| Paramètre | `comparison_field` (nom d'un autre champ, ou littéral numérique) |
| Message par défaut | `The :attribute must equal to :comparison_field.` |

```php
use oihana\validations\rules\EqualRule;
use Somnambulist\Components\Validation\Factory;

$validator = new Factory();
$validator->addRule( 'eq_field', new EqualRule() );

// Comparaison avec un autre champ
$validation = $validator->validate
(
    [
        'minimumPasswordLength'  => 8,
        'requiredPasswordLength' => 8,
    ],
    [
        'minimumPasswordLength' => 'required|integer|eq_field:requiredPasswordLength',
    ]
);

$validation->passes(); // true

// Comparaison avec une constante numérique fixe
$validation = $validator->validate(
    [ 'timeout' => 3600 ],
    [ 'timeout' => 'required|integer|eq_field:3600' ]
);

$validation->passes(); // true
```

## GreaterThanRule

Valide qu'une valeur est **strictement supérieure** au champ ou à la constante de comparaison (`$a > $b`).

| | |
|---|---|
| Classe | `oihana\validations\rules\GreaterThanRule` |
| Classe de base | `ComparisonRule` |
| Paramètre | `comparison_field` (nom d'un autre champ, ou littéral numérique) |
| Message par défaut | `The :attribute must be greater than :comparison_field.` |

```php
use oihana\validations\rules\GreaterThanRule;
use Somnambulist\Components\Validation\Factory;

$validator = new Factory();
$validator->addRule( 'gt_field', new GreaterThanRule() );

// Supérieur à un autre champ
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

// Supérieur à une valeur fixe (l'égalité ne suffit pas)
$validation = $validator->validate(
    [ 'timeout' => 600 ],
    [ 'timeout' => 'required|integer|gt_field:600' ]
);

$validation->fails(); // true — 600 n'est pas > 600
```

## GreaterThanOrEqualRule

Valide qu'une valeur est **supérieure ou égale** au champ ou à la constante de comparaison (`$a >= $b`).

| | |
|---|---|
| Classe | `oihana\validations\rules\GreaterThanOrEqualRule` |
| Classe de base | `ComparisonRule` |
| Paramètre | `comparison_field` (nom d'un autre champ, ou littéral numérique) |
| Message par défaut | `The :attribute must be greater than or equal to :comparison_field.` |

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

$validation->passes(); // true — l'égalité est autorisée
```

## LessThanRule

Valide qu'une valeur est **strictement inférieure** au champ ou à la constante de comparaison (`$a < $b`).

| | |
|---|---|
| Classe | `oihana\validations\rules\LessThanRule` |
| Classe de base | `ComparisonRule` |
| Paramètre | `comparison_field` (nom d'un autre champ, ou littéral numérique) |
| Message par défaut | `The :attribute must be less than :comparison_field.` |

```php
use oihana\validations\rules\LessThanRule;
use Somnambulist\Components\Validation\Factory;

$validator = new Factory();
$validator->addRule( 'lt_field', new LessThanRule() );

// Inférieur à une valeur fixe
$validation = $validator->validate(
    [ 'timeout' => 300 ],
    [ 'timeout' => 'required|integer|lt_field:600' ]
);

$validation->passes(); // true
```

## LessThanOrEqualRule

Valide qu'une valeur est **inférieure ou égale** au champ ou à la constante de comparaison (`$a <= $b`).

| | |
|---|---|
| Classe | `oihana\validations\rules\LessThanOrEqualRule` |
| Classe de base | `ComparisonRule` |
| Paramètre | `comparison_field` (nom d'un autre champ, ou littéral numérique) |
| Message par défaut | `The :attribute must be less than or equal to :comparison_field.` |

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

$validation->passes(); // true — l'égalité est autorisée
```

## RangeRule

Valide qu'une valeur numérique se situe dans un intervalle fermé `[min, max]` (les deux bornes **incluses**). Contrairement aux règles de comparaison, `RangeRule` prend **deux** paramètres, `min` et `max` (constantes `RangeRule::MIN` et `RangeRule::MAX`), et ne référence aucun autre champ.

| | |
|---|---|
| Classe | `oihana\validations\rules\RangeRule` |
| Classe de base | `AbstractRangeRule` |
| Paramètres | `min`, `max` (numériques ; les chaînes numériques sont acceptées) |
| Message par défaut | `The :attribute must be between :min and :max.` |

Les deux bornes sont converties avec `toNumber()`. Si `min` ou `max` est absent, ou ne peut pas être converti en nombre, une exception `Somnambulist\Components\Validation\Exceptions\ParameterException` est levée.

Utilisation autonome — renseignez explicitement les paramètres, puis appelez `check()` :

```php
use oihana\validations\rules\RangeRule;

$rule = new RangeRule();
$rule->fillParameters( [ 'min' => 0, 'max' => 100 ] );

$rule->check( 50 );  // true
$rule->check( 0 );   // true — la borne inférieure est incluse
$rule->check( 100 ); // true — la borne supérieure est incluse
$rule->check( 101 ); // false
$rule->check( 'foo' ); // false — non numérique
```

Enregistrée auprès de la `Factory`, les bornes sont indiquées dans la chaîne de règle sous la forme `range:min,max` :

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

## Classes de base

Si vous souhaitez construire votre propre règle de comparaison ou d'intervalle, étendez l'une des deux classes abstraites de base plutôt que de réimplémenter la logique de conversion numérique.

### `ComparisonRule`

`oihana\validations\rules\abstracts\ComparisonRule` étend la classe `Rule` de Somnambulist. Elle déclare le paramètre `comparison_field`, effectue la conversion `toNumber()` des deux opérandes, court-circuite à `false` sur les valeurs `null` ou non numériques, et délègue la décision réelle à une méthode abstraite que vous implémentez :

```php
abstract protected function compare( float|int $a , float|int $b ) : bool;
```

`$a` est la valeur de l'attribut, `$b` est la valeur de comparaison résolue. `EqualRule`, `GreaterThanRule`, `GreaterThanOrEqualRule`, `LessThanRule` et `LessThanOrEqualRule` n'implémentent chacune que cette unique méthode.

### `AbstractRangeRule`

`oihana\validations\rules\abstracts\AbstractRangeRule` étend également `Rule`. Les sous-classes définissent les bornes protégées `float|int $min` et `float|int $max` ; sa méthode `check()` convertit la valeur avec `toNumber()` et retourne `true` lorsque `$min <= valeur <= $max`. Elle expose les accesseurs `getMin()` et `getMax()`. `RangeRule` s'appuie dessus en lisant les bornes à partir des paramètres de règle `min`/`max`.

Voir [Custom rules](custom-rules.md) pour un guide complet sur l'écriture et l'enregistrement d'une règle au-dessus de ces classes abstraites.

## Voir aussi

- [Rules](rules.md) — fonctionnement des règles, enregistrement et référence des noms de règle.
- [ISO 8601 rules](iso8601.md) — règles de date, datetime, durée et combinées.
- [Custom rules](custom-rules.md) — écrire votre propre règle sur les classes abstraites fournies.
- [Documentation index](README.md) — retour à l'accueil de la documentation.

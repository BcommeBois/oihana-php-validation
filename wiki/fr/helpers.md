# Helpers

L'espace de noms `oihana\validations\rules\helpers` fournit **33 fonctions libres** (enregistrées via la clé `autoload.files` de composer) qui construisent des fragments de chaîne de règles au style Somnambulist/Laravel. Chaque fonction renvoie une petite chaîne telle que `between(0,120)` → `"between:0,120"`, que vous composez ensuite en une expression de validation complète.

Ce sont des fonctions globales, et non des méthodes de classe. Importez chacune d'elles avec une instruction `use function` :

```php
use function oihana\validations\rules\helpers\between;
use function oihana\validations\rules\helpers\min;
use function oihana\validations\rules\helpers\rules;
```

La plupart des bibliothèques de validation (Somnambulist Validation, Laravel) séparent plusieurs contraintes appliquées à un champ avec le caractère **pipe** (`|`), par exemple `'required|min:5|max:10'`. Deux helpers permettent de combiner les fragments produits par les autres :

- **`rule()`** construit un seul fragment `name[:value,...]` à partir d'un nom et de valeurs optionnelles.
- **`rules()`** assemble plusieurs fragments (chaînes ou tableaux) avec le pipe (`|`).

Utiliser ces helpers plutôt que des chaînes brutes garde vos définitions de règles vérifiées par le typage, refactorables et exemptes de chaînes magiques.

## Combiner les règles

| Fonction | Renvoie (exemple) | Signification |
|---|---|---|
| `rule( string $name , mixed ...$values )` | `rule('my_rule')` → `"my_rule"`<br>`rule('my_rule', 5)` → `"my_rule:5"`<br>`rule('my_rule', 5, 'hello')` → `"my_rule:5,hello"` | Construit un seul fragment de règle `name[:value1,value2,...]`. |
| `rules( string\|array ...$rules )` | `rules('required', min(5), max(10))` → `"required\|min:5\|max:10"`<br>`rules(['required', 'min:5', 'max:10'])` → `"required\|min:5\|max:10"` | Concatène plusieurs fragments de règles (chaînes ou tableaux) avec le pipe (`\|`). |

## Présence et obligation

| Fonction | Renvoie (exemple) | Signification |
|---|---|---|
| `requires( string ...$fields )` | `requires('email', 'password')` → `"requires:email,password"` | Les champs listés doivent être présents et non vides (échoue avec `sometimes`/`nullable`). |
| `requiredIf( string $anotherField , mixed ...$values )` | `requiredIf('name', 'foo', 'bar')` → `"required_if:name,foo,bar"` | Obligatoire quand `anotherField` est égal à l'une des valeurs. |
| `requiredUnless( string $anotherField , mixed ...$values )` | `requiredUnless('name', 'foo', 'bar')` → `"required_unless:name,foo,bar"` | Obligatoire sauf si `anotherField` est égal à l'une des valeurs. |
| `requiredWith( string ...$fields )` | `requiredWith('email', 'password')` → `"required_with:email,password"` | Obligatoire quand **l'un** des champs listés est présent. |
| `requiredWithAll( string ...$fields )` | `requiredWithAll('email', 'password')` → `"required_with_all:email,password"` | Obligatoire quand **tous** les champs listés sont présents. |
| `requiredWithout( string ...$fields )` | `requiredWithout('email', 'password')` → `"required_without:email,password"` | Obligatoire quand **l'un** des champs listés est absent. |
| `requiredWithoutAll( string ...$fields )` | `requiredWithoutAll('email', 'password')` → `"required_without_all:email,password"` | Obligatoire quand **tous** les champs listés sont absents. |
| `prohibitedIf( string $anotherField , mixed ...$values )` | `prohibitedIf('password', 'foo', 'bar')` → `"prohibited_if:password,foo,bar"` | Interdit quand `anotherField` fournit l'une des valeurs. |
| `prohibitedUnless( string $anotherField , mixed ...$values )` | `prohibitedUnless('password', 'foo', 'bar')` → `"prohibited_unless:password,foo,bar"` | Interdit sauf si `anotherField` a l'une des valeurs. |
| `defaultValue( mixed $value )` | `defaultValue(1)` → `"default:1"` | Utilise cette valeur par défaut dans les données validées quand l'attribut n'a pas de valeur. |

## Taille et valeurs numériques

| Fonction | Renvoie (exemple) | Signification |
|---|---|---|
| `between( string\|int\|float $min , string\|int\|float $max )` | `between(10, 20)` → `"between:10,20"`<br>`between('1M', '2M')` → `"between:1M,2M"` | La taille doit être comprise entre `min` et `max` (fonctionne aussi sur la taille des fichiers envoyés). |
| `min( string\|int\|float $value )` | `min(2)` → `"min:2"`<br>`min(-90)` → `"min:-90"`<br>`min('1M')` → `"min:1M"` | Taille supérieure ou égale à la valeur donnée. |
| `max( string\|int\|float $value )` | `max(10)` → `"max:10"`<br>`max('2M')` → `"max:2M"` | Taille inférieure ou égale à la valeur donnée. |
| `length( string\|int $value )` | `length(10)` → `"length:10"` | Chaîne d'exactement la longueur donnée. |
| `digits( int $value )` | `digits(4)` → `"digits:4"` | Valeur numérique d'une longueur exacte de `value` chiffres. |
| `digitsBetween( int $min , int $max )` | `digitsBetween(2, 5)` → `"digits_between:2,5"` | Valeur numérique dont la longueur est comprise entre `min` et `max`. |

## Chaînes de caractères

| Fonction | Renvoie (exemple) | Signification |
|---|---|---|
| `startsWith( string $anotherField )` | `startsWith('prefix')` → `"starts_with:prefix"` | La valeur doit commencer par `anotherField`. |
| `endsWith( string $anotherField )` | `endsWith('suffix')` → `"ends_with:suffix"` | La valeur doit se terminer par `anotherField`. |
| `regex( string $regex )` | `regex('/(this\|that\|value)/')` → `"regex:/(this\|that\|value)/"` | La valeur doit correspondre à l'expression régulière donnée. |
| `url( null\|array\|string $scheme = null )` | `url()` → `"url"`<br>`url('http')` → `"url:http"`<br>`url('http,https')` → `"url:http,https"`<br>`url(['http','https'])` → `"url:http,https"`<br>`url('ftp')` → `"url:ftp"` | Format d'URL valide, éventuellement restreint au(x) schéma(s) donné(s). |

## Appartenance à un ensemble et égalité

| Fonction | Renvoie (exemple) | Signification |
|---|---|---|
| `in( string ...$values )` | `in('foo', 'bar')` → `"in:foo,bar"` | La valeur doit figurer dans la liste donnée. |
| `notIn( string ...$values )` | `notIn('foo', 'bar')` → `"not_in:foo,bar"` | La valeur ne doit pas figurer dans la liste donnée. |
| `same( string $anotherField )` | `same('password')` → `"same:password"` | La valeur doit être égale à celle de `anotherField`. |
| `different( string $anotherField )` | `different('name')` → `"different:name"` | La valeur doit être différente de celle de `anotherField`. |

## Fichiers

| Fonction | Renvoie (exemple) | Signification |
|---|---|---|
| `extension( string ...$values )` | `extension('jpg', 'png')` → `"extension:jpg,png"` | Le chemin/l'URL doit se terminer par l'une des extensions listées (pour les chemins/URLs). |
| `mimes( string ...$values )` | `mimes('jpg', 'png')` → `"mimes:jpg,png"` | L'élément `$_FILES` envoyé doit correspondre au type MIME de l'une des extensions listées. |

## Dates

| Fonction | Renvoie (exemple) | Signification |
|---|---|---|
| `after( string $date )` | `after('2016-12-31')` → `"after:2016-12-31"` | La date doit être postérieure à la date donnée (tout ce que `strtotime` sait analyser). |
| `before( string $date )` | `before('2016-12-31')` → `"before:2016-12-31"` | La date doit être antérieure à la date donnée (tout ce que `strtotime` sait analyser). |
| `date( ?string $format = null )` | `date()` → `"date"`<br>`date('Y-m-d')` → `"date:Y-m-d"` | Date valide suivant le format donné (par défaut `Y-m-d`). |

## Tableaux

| Fonction | Renvoie (exemple) | Signification |
|---|---|---|
| `arrayMustHaveKeys( string ...$values )` | `arrayMustHaveKeys('foo', 'bar')` → `"array_must_have_keys:foo,bar"` | Le tableau doit contenir toutes les clés listées (les clés supplémentaires sont autorisées). |
| `arrayCanOnlyHaveKeys( string ...$values )` | `arrayCanOnlyHaveKeys('foo', 'bar')` → `"array_can_only_have_keys:foo,bar"` | Le tableau ne peut contenir que les clés listées ; toute autre clé fait échouer la validation. |

## Un exemple combiné

Les helpers prennent tout leur sens lorsqu'ils sont composés avec `rules()` pour construire des chaînes de règles complètes et lisibles :

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

## Voir aussi

- [Rules](rules.md)
- [Custom rules](custom-rules.md)
- Retour à l'[index de la documentation](README.md)

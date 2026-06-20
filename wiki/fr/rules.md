# Règles

Chaque règle de `oihana/php-validation` étend la classe Somnambulist `Rule` : elles partagent donc toutes le même cycle de vie — une méthode `check( mixed $value ): bool`, un message d'erreur `$message` qui interprète `:attribute`, et des `$fillableParams` optionnels. Une règle s'utilise de trois façons : appeler directement `check()` pour un test ponctuel, l'enregistrer sur une `Somnambulist\Components\Validation\Factory` sous un nom puis référencer ce nom dans une chaîne de règles, ou composer la chaîne de règles elle-même avec les [helpers](helpers.md) chargés automatiquement. Le README présente déjà ces trois modes en survol ; cette page approfondit l'**enregistrement** d'une règle et les **noms de règles** canoniques exposés par `enums\Rules`.

## Enregistrer une règle

Pour utiliser une règle dans une chaîne de règles séparée par des barres verticales, on l'enregistre d'abord sur une `Factory` avec `addRule( string $name, Rule $rule )`. Le nom choisi est le jeton qui apparaît dans la chaîne de règles ; ce même nom est ensuite résolu par le validateur lorsqu'il analyse les règles de chaque champ.

```php
use Somnambulist\Components\Validation\Factory;
use oihana\validations\rules\ColorRule;

$factory = new Factory();
$factory->addRule( 'color', new ColorRule() );

$validation = $factory->validate(
    [ 'background' => '#ff00ff' ],
    [ 'background' => 'required|color' ]
);

$validation->passes(); // true
```

Les règles qui prennent des arguments de constructeur sont configurées au moment de l'enregistrement : tous les champs partageant le nom enregistré sont donc validés avec la même configuration.

```php
use Somnambulist\Components\Validation\Factory;
use oihana\validations\rules\I18nRule;

$factory = new Factory();
$factory->addRule( 'i18n', new I18nRule( [ 'fr', 'en' ] ) );

$validation = $factory->validate(
    [ 'description' => [ 'fr' => 'Bonjour', 'en' => 'Hello' ] ],
    [ 'description' => 'required|array|i18n' ]
);

$validation->passes(); // true
```

Le nom passé à `addRule()` est arbitraire, mais utiliser la valeur canonique de `enums\Rules` (par exemple `Rules::COLOR` plutôt que le littéral `'color'`) maintient la cohérence entre les enregistrements et les chaînes de règles à travers tout le code.

## La référence `Rules`

`oihana\validations\enums\Rules` n'est **pas** un enum PHP natif : c'est une simple classe utilisant `ConstantsTrait`, qui contient les noms de règles canoniques sous forme de membres `public const string`. Chaque constante associe un identifiant lisible (`Rules::GREATER_THAN`) au jeton réellement écrit dans une chaîne de règles (`'gt'`). Certaines constantes nomment les règles oihana fournies par cette bibliothèque (couleur, géo, ISO 8601, …) ; d'autres reflètent les règles Somnambulist intégrées, afin qu'un même vocabulaire couvre les deux. C'est la valeur de la constante — et non son nom — que l'on écrit dans une chaîne de règles.

Comme elle utilise `ConstantsTrait`, la classe expose aussi les helpers de réflexion habituels (par exemple énumérer ou valider les constantes déclarées) sans définir d'enum natif.

### Règles de comparaison oihana

| Constante | Valeur | Signification |
| --- | --- | --- |
| `Rules::EQUAL` | `equal` | La valeur est égale à un autre champ ou à une constante numérique fixe (`EqualRule`). |
| `Rules::GREATER_THAN` | `gt` | La valeur est supérieure à un autre champ ou à une constante (`GreaterThanRule`). |
| `Rules::GREATER_THAN_OR_EQUAL` | `gte` | La valeur est supérieure ou égale à un autre champ ou à une constante (`GreaterThanOrEqualRule`). |
| `Rules::LESS_THAN` | `lt` | La valeur est inférieure à un autre champ ou à une constante (`LessThanRule`). |
| `Rules::LESS_THAN_OR_EQUAL` | `lte` | La valeur est inférieure ou égale à un autre champ ou à une constante (`LessThanOrEqualRule`). |
| `Rules::RANGE` | `range` | La valeur numérique est comprise entre un minimum et un maximum, bornes incluses (`RangeRule`). |

Voir [Règles de comparaison et d'intervalle](comparison.md) pour les détails.

### Règles ISO 8601 oihana

| Constante | Valeur | Signification |
| --- | --- | --- |
| `Rules::ISO8601_DATE_TIME` | `iso8601_date_time` | Expression date-heure ISO 8601 valide (`ISO8601DateTimeRule`). |
| `Rules::ISO8601_DURATION` | `iso8601_duration` | Expression de durée ISO 8601 valide (`ISO8601DurationRule`). |
| `Rules::ISO8601_DATE_TIME_OR_DURATION` | `iso8601_date_time_or_duration` | Soit une date-heure, soit une durée ISO 8601 valide (`ISO8601DateTimeOrDurationRule`). |

Voir [Règles ISO 8601](iso8601.md) pour les détails.

### Règles géo oihana

| Constante | Valeur | Signification |
| --- | --- | --- |
| `Rules::LATITUDE` | `latitude` | Latitude géographique valide (`LatitudeRule`). |
| `Rules::LONGITUDE` | `longitude` | Longitude géographique valide (`LongitudeRule`). |
| `Rules::ELEVATION` | `elevation` | Altitude (élévation) valide en mètres (`ElevationRule`). |

### Règles i18n et diverses oihana

| Constante | Valeur | Signification |
| --- | --- | --- |
| `Rules::COLOR` | `color` | La valeur correspond à une expression de couleur, p. ex. `#ff0000` (`ColorRule`). |

La règle de carte `i18n` montrée plus haut s'enregistre par son nom de la même manière ; voir [Règles i18n et codes postaux](i18n.md).

### Règles Somnambulist intégrées (passthrough)

`Rules` redéclare également les noms des règles fournies par Somnambulist, afin qu'un seul vocabulaire couvre à la fois les règles intégrées et les règles oihana. Ce sont des noms passthrough : ils documentent et référencent les règles amont sans ajouter de comportement nouveau. Une sélection représentative :

| Constante | Valeur | Signification |
| --- | --- | --- |
| `Rules::REQUIRED` | `required` | Le champ doit être présent et non vide. |
| `Rules::NULLABLE` | `nullable` | Le champ peut être vide. |
| `Rules::SOMETIMES` | `sometimes` | Le champ peut être absent ou null ; validé uniquement s'il est présent. |
| `Rules::ARRAY` | `array` | La valeur doit être un tableau. |
| `Rules::BOOLEAN` | `boolean` | La valeur doit être booléenne (`true`, `false`, `1`, `0`, `"1"`, `"0"`). |
| `Rules::INTEGER` | `integer` | La valeur doit être un entier. |
| `Rules::FLOAT` | `float` | La valeur doit être un flottant. |
| `Rules::STRING` | `string` | La valeur doit être une chaîne PHP. |
| `Rules::IN` | `in` | La valeur fait partie d'une liste donnée. |
| `Rules::NOT_IN` | `not_in` | La valeur ne fait pas partie d'une liste donnée. |
| `Rules::BETWEEN` | `between` | La taille est comprise entre un min et un max. |
| `Rules::MIN` | `min` | La taille est au moins égale au nombre donné. |
| `Rules::MAX` | `max` | La taille est au plus égale au nombre donné. |
| `Rules::LENGTH` | `length` | La chaîne a exactement la longueur donnée. |
| `Rules::DIGITS` | `digits` | Valeur numérique d'un nombre exact de chiffres. |
| `Rules::DIGITS_BETWEEN` | `digits_between` | Valeur numérique dont le nombre de chiffres est dans un intervalle. |
| `Rules::REGEX` | `regex` | La valeur correspond à une expression régulière. |
| `Rules::DATE` | `date` | Date valide pour un format donné (défaut `Y-m-d`). |
| `Rules::AFTER` / `Rules::BEFORE` | `after` / `before` | Date après / avant une borne analysable par `strtotime`. |
| `Rules::EMAIL` | `email` | Adresse e-mail valide. |
| `Rules::URL` | `url` | URL valide (restriction de schéma optionnelle). |
| `Rules::UUID` | `uuid` | UUID valide et non nul. |
| `Rules::JSON` | `json` | Chaîne JSON valide. |
| `Rules::IP` / `Rules::IPV4` / `Rules::IPV6` | `ipv` / `ipv4` / `ipv6` | Adresse IP valide de la famille correspondante. |
| `Rules::ALPHA` / `Rules::ALPHA_NUM` / `Rules::ALPHA_DASH` / `Rules::ALPHA_SPACES` | `alpha` / `alpha_num` / `alpha_dash` / `alpha_spaces` | Contraintes de classes de caractères. |
| `Rules::SAME` / `Rules::DIFFERENT` | `same` / `different` | La valeur égale / diffère d'un autre champ. |
| `Rules::STARTS_WITH` / `Rules::ENDS_WITH` | `starts_with` / `ends_with` | La valeur commence / finit par un autre champ. |
| `Rules::REQUIRED_IF` / `Rules::REQUIRED_UNLESS` | `required_if` / `required_unless` | Présence conditionnelle. |
| `Rules::REQUIRED_WITH` / `Rules::REQUIRED_WITH_ALL` | `required_with` / `required_with_all` | Présence liée à d'autres champs. |
| `Rules::REQUIRED_WITHOUT` / `Rules::REQUIRED_WITHOUT_ALL` | `required_without` / `required_without_all` | Présence liée à l'absence d'autres champs. |
| `Rules::REQUIRES` | `requires` | D'autres champs doivent être présents et non vides. |
| `Rules::PROHIBITED` / `Rules::PROHIBITED_IF` / `Rules::PROHIBITED_UNLESS` | `prohibited` / `prohibited_if` / `prohibited_unless` | Champ interdit (sous condition). |
| `Rules::ACCEPTED` / `Rules::REJECTED` | `accepted` / `rejected` | Valeurs d'acceptation vraies / fausses. |
| `Rules::PRESENT` | `present` | Le champ doit être présent, quelle que soit la valeur. |
| `Rules::DEFAULT` | `default` | Valeur par défaut utilisée quand le champ est absent. |
| `Rules::CALLBACK` | `callback` | Validation par closure personnalisée (syntaxe tableau uniquement). |
| `Rules::ANY_OF` | `any` | Toutes les valeurs séparées par des virgules doivent figurer dans l'ensemble donné. |
| `Rules::ARRAY_MUST_HAVE_KEYS` / `Rules::ARRAY_CAN_ONLY_HAVE_KEYS` | `array_must_have_keys` / `array_can_only_have_keys` | Contraintes sur les clés d'un tableau. |
| `Rules::LOWERCASE` / `Rules::UPPERCASE` | `lowercase` / `uppercase` | Contraintes de casse. |
| `Rules::EXTENSION` / `Rules::MIMES` | `extension` / `mimes` | Contraintes d'extension de fichier / de type MIME. |

Pour la liste complète et faisant autorité des règles intégrées et de leurs paramètres, voir la documentation [Somnambulist available rules](https://github.com/somnambulist-tech/validation?tab=readme-ov-file#available-rules).

## Voir aussi

- [Règles de comparaison et d'intervalle](comparison.md) — `EqualRule`, `GreaterThanRule`, `LessThanRule`, `RangeRule`.
- [Règles ISO 8601](iso8601.md) — date, date-heure, durée et règles combinées.
- [Helpers](helpers.md) — les fonctions de chaînes de règles chargées automatiquement.
- [Règles personnalisées](custom-rules.md) — écrire votre propre règle sur les classes abstraites fournies.
- [Index de la documentation](README.md) — retour à la table des matières.

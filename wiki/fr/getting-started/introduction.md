# Introduction

`oihana/php-validation` rassemble les briques de validation qui vivaient auparavant dans `oihana/php-system`, extraites dans un paquet dédié afin qu'un projet puisse dépendre de la couche de validation **sans** tirer une pile HTTP, un moteur de templates ou une couche de routage.

Elle s'appuie sur [Somnambulist Validation](https://github.com/somnambulist-tech/validation) : chaque règle étend la classe `Rule` de Somnambulist, et fonctionne donc aussi bien en autonome qu'au sein d'une `Factory` Somnambulist. Par-dessus, la bibliothèque ajoute un ensemble de **fonctions helpers** expressives qui construisent des expressions de chaîne de règles.

## Ce qu'elle fournit

| Composant | Type | Rôle |
|---|---|---|
| `rules\abstracts\ComparisonRule` | abstraite | Base des règles de comparaison numérique (`gt`, `gte`, `lt`, `lte`, …). |
| `rules\abstracts\AbstractRangeRule` | abstraite | Base des règles d'intervalle min/max. |
| `rules\abstracts\ContainerRule` | abstraite | Base des règles nécessitant un conteneur PSR-11 (règles liées aux modèles). |
| `rules\EqualRule` / `GreaterThanRule` / `LessThanRule` / … | classes | Règles de comparaison. |
| `rules\RangeRule` | classe | Règle d'intervalle numérique. |
| `rules\ISO8601DateRule` / `ISO8601DateTimeRule` / `ISO8601DurationRule` | classes | Règles temporelles ISO 8601. |
| `rules\geo\LatitudeRule` / `LongitudeRule` / `ElevationRule` | classes | Règles de coordonnées géographiques. |
| `rules\I18nRule` / `PostalCodeRule` | classes | Validations localisées. |
| `rules\models\ExistModelRule` / `UniqueModelRule` | classes | Règles liées aux modèles (adossées au conteneur). |
| `rules\auth\EffectRule` / `JWTAlgorithmRule` / `rules\http\HttpMethodRule` | classes | Règles auth et HTTP. |
| `rules\ColorRule` / `ConstantsRule` / `InstanceOfRule` / `StartsWithRule` | classes | Règles à usage général. |
| `rules\helpers\*` | fonctions libres | 33 constructeurs de chaîne de règles (`between()`, `requiredIf()`, …). |
| `enums\Rules` | classe | Référence basée sur `ConstantsTrait` de chaque nom de règle (pas d'enum natif). |

## La philosophie *oihana*

- **PHP 8.4+ uniquement** — constantes typées, aucun palliatif legacy.
- **Pas de *magic strings*** — les noms de règles sont des constantes typées sur la classe `Rules` ; le projet n'utilise jamais d'enum natif PHP.
- **Composable** — chaque règle a une responsabilité unique ; les helpers composent les chaînes de règles.
- **Testée** — 100 % de couverture de lignes, mode strict PHPUnit (voir [Tests & couverture](../testing.md)).

## Étapes suivantes

- [Installation](installation.md)
- [Dépendances](dependencies.md)
- [Règles](../rules.md)

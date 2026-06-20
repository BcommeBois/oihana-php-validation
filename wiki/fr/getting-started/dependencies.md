# Dépendances

`oihana/php-validation` conserve une empreinte runtime réduite. Voici ce qu'elle
requiert et **pourquoi**.

## Dépendances runtime

| Paquet | Rôle |
|---|---|
| [`somnambulist/validation`](https://github.com/somnambulist-tech/validation) | Le moteur de validation : chaque règle étend sa classe `Rule` et s'utilise via sa `Factory`. |
| [`oihana/php-core`](https://github.com/BcommeBois/oihana-php-core) | Helpers de base — `strings\compile()` (construction de chaîne de règles), `toNumber()`, `arrays\toArray()`. |
| [`oihana/php-enums`](https://github.com/BcommeBois/oihana-php-enums) | Constantes typées utilisées par les règles — `Char`, `PostalCodePattern`, `http\HttpMethod`. |
| [`oihana/php-reflect`](https://github.com/BcommeBois/oihana-php-reflect) | `ConstantsTrait` (la classe de référence `Rules`) et utilitaires de réflexion. |
| [`oihana/php-standards`](https://github.com/BcommeBois/oihana-php-standards) | Standards ISO — `org\iso\ISO3166_1` et les helpers ISO 8601 derrière les règles de date. |
| [`oihana/php-schema`](https://github.com/BcommeBois/oihana-php-schema) | Constantes Schema.org (`org\schema\constants\Schema`) utilisées par les règles liées aux modèles. |
| [`oihana/php-models`](https://github.com/BcommeBois/oihana-php-models) | Les contrats `DocumentsModel` / `ExistModel` que résolvent `ExistModelRule` et `UniqueModelRule`. |
| [`oihana/php-logging`](https://github.com/BcommeBois/oihana-php-logging) | Journalisation PSR-3 (`LoggerTrait`) utilisée par les règles adossées au conteneur. |
| [`oihana/php-traits`](https://github.com/BcommeBois/oihana-php-traits) | Traits d'objets réutilisables (`ToStringTrait`) utilisés par les règles abstraites. |
| [`psr/container`](https://packagist.org/packages/psr/container) | Contrat PSR-11 `ContainerInterface` utilisé par `ContainerRule`. |

## Dépendances de développement

| Paquet | Rôle |
|---|---|
| `phpunit/phpunit` | Lanceur de tests (mode strict). |
| `nunomaduro/collision` | Sortie d'erreurs CLI lisible. |
| `phpdocumentor/shim` | Génération de la documentation API. |
| `php-di/php-di` | Conteneur PSR-11 utilisé dans les tests des règles liées aux modèles. |

## Étapes suivantes

- [Règles](../rules.md)
- [Règles liées aux modèles](../models.md)

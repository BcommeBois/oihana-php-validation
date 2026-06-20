# Dependencies

`oihana/php-validation` keeps a focused runtime footprint. Here is what it
requires and **why**.

## Runtime dependencies

| Package | Role |
|---|---|
| [`somnambulist/validation`](https://github.com/somnambulist-tech/validation) | The validation engine: every rule extends its `Rule` class and is used through its `Factory`. |
| [`oihana/php-core`](https://github.com/BcommeBois/oihana-php-core) | Core helpers — `strings\compile()` (rule-string building), `toNumber()`, `arrays\toArray()`. |
| [`oihana/php-enums`](https://github.com/BcommeBois/oihana-php-enums) | Typed constants used by the rules — `Char`, `PostalCodePattern`, `http\HttpMethod`. |
| [`oihana/php-reflect`](https://github.com/BcommeBois/oihana-php-reflect) | `ConstantsTrait` (the `Rules` reference class) and reflection utilities. |
| [`oihana/php-standards`](https://github.com/BcommeBois/oihana-php-standards) | ISO standards — `org\iso\ISO3166_1` and the ISO 8601 helpers behind the date rules. |
| [`oihana/php-schema`](https://github.com/BcommeBois/oihana-php-schema) | Schema.org constants (`org\schema\constants\Schema`) used by the model-aware rules. |
| [`oihana/php-models`](https://github.com/BcommeBois/oihana-php-models) | The `DocumentsModel` / `ExistModel` contracts the `ExistModelRule` and `UniqueModelRule` resolve. |
| [`oihana/php-logging`](https://github.com/BcommeBois/oihana-php-logging) | PSR-3 logging (`LoggerTrait`) used by the container-backed rules. |
| [`oihana/php-traits`](https://github.com/BcommeBois/oihana-php-traits) | Reusable object traits (`ToStringTrait`) used by the abstract rules. |
| [`psr/container`](https://packagist.org/packages/psr/container) | PSR-11 `ContainerInterface` contract used by `ContainerRule`. |

## Development dependencies

| Package | Role |
|---|---|
| `phpunit/phpunit` | Test runner (strict mode). |
| `nunomaduro/collision` | Readable CLI error output. |
| `phpdocumentor/shim` | API documentation generation. |
| `php-di/php-di` | PSR-11 container used in the model-aware rule tests. |

## Next steps

- [Rules](../rules.md)
- [Model-aware rules](../models.md)

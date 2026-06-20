# Introduction

`oihana/php-validation` gathers the validation building blocks that used to live inside `oihana/php-system`, extracted into a focused package so a project can depend on the validation layer **without** pulling an HTTP stack, a templating engine or a routing layer.

It builds on [Somnambulist Validation](https://github.com/somnambulist-tech/validation): every rule extends the Somnambulist `Rule` class, so it works both standalone and inside a Somnambulist `Factory`. On top of that, the library adds a set of expressive **helper functions** that build rule-string expressions.

## What it provides

| Component | Type | Role |
|---|---|---|
| `rules\abstracts\ComparisonRule` | abstract | Base for numeric comparison rules (`gt`, `gte`, `lt`, `lte`, …). |
| `rules\abstracts\AbstractRangeRule` | abstract | Base for min/max range rules. |
| `rules\abstracts\ContainerRule` | abstract | Base for rules that need a PSR-11 container (model-aware rules). |
| `rules\EqualRule` / `GreaterThanRule` / `LessThanRule` / … | classes | Comparison rules. |
| `rules\RangeRule` | class | Numeric range rule. |
| `rules\ISO8601DateRule` / `ISO8601DateTimeRule` / `ISO8601DurationRule` | classes | ISO 8601 temporal rules. |
| `rules\geo\LatitudeRule` / `LongitudeRule` / `ElevationRule` | classes | Geographic coordinate rules. |
| `rules\I18nRule` / `PostalCodeRule` | classes | Localized validations. |
| `rules\models\ExistModelRule` / `UniqueModelRule` | classes | Model-aware rules (container-backed). |
| `rules\auth\EffectRule` / `JWTAlgorithmRule` / `rules\http\HttpMethodRule` | classes | Auth and HTTP rules. |
| `rules\ColorRule` / `ConstantsRule` / `InstanceOfRule` / `StartsWithRule` | classes | General-purpose rules. |
| `rules\helpers\*` | free functions | 33 rule-string builders (`between()`, `requiredIf()`, …). |
| `enums\Rules` | class | `ConstantsTrait`-based reference of every rule name (no native enums). |

## The *oihana* philosophy

- **PHP 8.4+ only** — typed constants, no legacy shims.
- **No *magic strings*** — rule names are typed constants on the `Rules` class; the project never uses native PHP enums.
- **Composable** — each rule has a single responsibility; helpers compose rule strings.
- **Tested** — 100% line coverage, strict PHPUnit mode (see [Tests & coverage](../testing.md)).

## Next steps

- [Installation](installation.md)
- [Dependencies](dependencies.md)
- [Rules](../rules.md)

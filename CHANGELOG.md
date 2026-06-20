# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-06-20

First release. The `oihana\validations` namespace is extracted from
`oihana/php-system` into its own focused validation package for PHP 8.4+,
built on [Somnambulist Validation](https://github.com/somnambulist-tech/validation).

### Added
- Project scaffolding: `composer.json`, `phpunit.xml`, `phpdoc.xml`,
  CI and Docs GitHub workflows, coverage tooling, phpDocumentor template,
  README, CONTRIBUTING and license.
- Brand assets (logos) under `assets/images/`.
- The `oihana\validations` library, imported from `oihana/php-system`
  (identical FQNs):
  - Comparison & range rules — `rules\EqualRule`, `rules\GreaterThanRule`,
    `rules\GreaterThanOrEqualRule`, `rules\LessThanRule`,
    `rules\LessThanOrEqualRule`, `rules\RangeRule`, on the
    `rules\abstracts\ComparisonRule` / `AbstractRangeRule` base classes.
  - ISO 8601 rules — `rules\ISO8601DateRule`, `rules\ISO8601DateTimeRule`,
    `rules\ISO8601DurationRule`, `rules\ISO8601DateTimeOrDurationRule`.
  - Geo rules — `rules\geo\LatitudeRule`, `rules\geo\LongitudeRule`,
    `rules\geo\ElevationRule`.
  - i18n & postal rules — `rules\I18nRule`, `rules\PostalCodeRule`.
  - Model-aware rules — `rules\models\ExistModelRule`,
    `rules\models\UniqueModelRule`, on the `rules\abstracts\ContainerRule`
    base class.
  - Auth & HTTP rules — `rules\auth\EffectRule`, `rules\auth\JWTAlgorithmRule`,
    `rules\http\HttpMethodRule`.
  - General-purpose rules — `rules\ColorRule`, `rules\ConstantsRule`,
    `rules\InstanceOfRule`, `rules\StartsWithRule`.
  - `enums\Rules` — a `ConstantsTrait`-based reference of every rule name
    (no native enums).
  - 33 rule-string helper free functions under `rules\helpers\*`, wired via
    composer `autoload.files`.
- Unit-test suite imported from `oihana/php-system` (PHPUnit, strict mode),
  plus the `tests\oihana\models\mocks\MockDocumentsModel` fixture used by the
  model-aware rule tests. **100% line coverage** (283/283 lines, 48/48 methods,
  24/24 classes), 269 tests.
- Bilingual user guide under `wiki/` (English + French): getting started
  (introduction, installation, dependencies), rules, comparison & range,
  ISO 8601, geo, i18n & postal, model-aware rules, other rules, helpers,
  custom rules and a testing guide.

### Fixed
- `enums\Rules` referenced PHPUnit's `PHPUnit\Framework\Constraint\GreaterThan`
  through a stray `use` import and a `@see GreaterThan` tag on the `GREATER_THAN`
  constant — a copy-paste leftover. Pointed the `@see` at the library's own
  `rules\GreaterThanRule` and removed the dead PHPUnit import.

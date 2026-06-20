# Rules

Every rule in `oihana/php-validation` extends the Somnambulist `Rule` class, so they all share the same lifecycle: a `check( mixed $value ): bool` method, a `:attribute`-aware error `$message`, and optional `$fillableParams`. You can use a rule three ways — call `check()` on it directly for a one-off test, register it on a `Somnambulist\Components\Validation\Factory` under a name and reference that name inside a rule string, or compose the rule string itself with the autoloaded [helpers](helpers.md). The README already walks through the three modes at a glance; this page goes one level deeper on **registering** a rule and on the canonical **rule names** exposed by `enums\Rules`.

## Registering a rule

To use a rule inside a pipe-delimited rule string, you first register it on a `Factory` with `addRule( string $name, Rule $rule )`. The name you choose is the token that appears in the rule string; the same name is later resolved by the validator when it parses each field's rules.

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

Rules that take constructor arguments are configured at registration time, so every field sharing the registered name validates against the same configuration:

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

The name passed to `addRule()` is arbitrary, but using the canonical value from `enums\Rules` (for example `Rules::COLOR` instead of the literal `'color'`) keeps registrations and rule strings consistent across the codebase.

## The `Rules` reference

`oihana\validations\enums\Rules` is **not** a native PHP enum — it is a plain class using `ConstantsTrait`, holding the canonical rule-name strings as `public const string` members. Each constant maps a readable identifier (`Rules::GREATER_THAN`) to the token actually written in a rule string (`'gt'`). Some constants name the oihana rules shipped by this library (color, geo, ISO 8601, …); others mirror the built-in Somnambulist rules so the same vocabulary covers both. The constant value — not the constant name — is what you write in a rule string.

Because it uses `ConstantsTrait`, the class also exposes the usual reflection helpers (such as enumerating or validating the declared constants) without defining a native enum.

### oihana comparison rules

| Constant | Value | Meaning |
| --- | --- | --- |
| `Rules::EQUAL` | `equal` | Value is equal to another field or a fixed numeric constant (`EqualRule`). |
| `Rules::GREATER_THAN` | `gt` | Value is greater than another field or a constant (`GreaterThanRule`). |
| `Rules::GREATER_THAN_OR_EQUAL` | `gte` | Value is greater than or equal to another field or a constant (`GreaterThanOrEqualRule`). |
| `Rules::LESS_THAN` | `lt` | Value is less than another field or a constant (`LessThanRule`). |
| `Rules::LESS_THAN_OR_EQUAL` | `lte` | Value is less than or equal to another field or a constant (`LessThanOrEqualRule`). |
| `Rules::RANGE` | `range` | Numeric value lies between a minimum and a maximum, inclusive (`RangeRule`). |

See [Comparison & range rules](comparison.md) for the details.

### oihana ISO 8601 rules

| Constant | Value | Meaning |
| --- | --- | --- |
| `Rules::ISO8601_DATE_TIME` | `iso8601_date_time` | Valid ISO 8601 date-time expression (`ISO8601DateTimeRule`). |
| `Rules::ISO8601_DURATION` | `iso8601_duration` | Valid ISO 8601 duration expression (`ISO8601DurationRule`). |
| `Rules::ISO8601_DATE_TIME_OR_DURATION` | `iso8601_date_time_or_duration` | Either a valid ISO 8601 date-time or duration (`ISO8601DateTimeOrDurationRule`). |

See [ISO 8601 rules](iso8601.md) for the details.

### oihana geo rules

| Constant | Value | Meaning |
| --- | --- | --- |
| `Rules::LATITUDE` | `latitude` | Valid geographic latitude (`LatitudeRule`). |
| `Rules::LONGITUDE` | `longitude` | Valid geographic longitude (`LongitudeRule`). |
| `Rules::ELEVATION` | `elevation` | Valid elevation (altitude) in meters (`ElevationRule`). |

### oihana i18n & misc rules

| Constant | Value | Meaning |
| --- | --- | --- |
| `Rules::COLOR` | `color` | Value matches a color expression, e.g. `#ff0000` (`ColorRule`). |

The `i18n` map rule shown above is registered by name in the same way; see [i18n & postal rules](i18n.md).

### Built-in Somnambulist rules (passthrough)

`Rules` also re-declares the names of the rules that ship with Somnambulist, so a single vocabulary covers built-in and oihana rules alike. These are passthrough names — they document and reference the upstream rules rather than adding new behaviour. A representative selection:

| Constant | Value | Meaning |
| --- | --- | --- |
| `Rules::REQUIRED` | `required` | Field must be present and non-empty. |
| `Rules::NULLABLE` | `nullable` | Field may be empty. |
| `Rules::SOMETIMES` | `sometimes` | Field may be absent or null; validated only when present. |
| `Rules::ARRAY` | `array` | Value must be an array. |
| `Rules::BOOLEAN` | `boolean` | Value must be boolean (`true`, `false`, `1`, `0`, `"1"`, `"0"`). |
| `Rules::INTEGER` | `integer` | Value must be an integer. |
| `Rules::FLOAT` | `float` | Value must be a float. |
| `Rules::STRING` | `string` | Value must be a PHP string. |
| `Rules::IN` | `in` | Value is one of a given list. |
| `Rules::NOT_IN` | `not_in` | Value is not in a given list. |
| `Rules::BETWEEN` | `between` | Size is between a min and a max. |
| `Rules::MIN` | `min` | Size is at least the given number. |
| `Rules::MAX` | `max` | Size is at most the given number. |
| `Rules::LENGTH` | `length` | String has exactly the given length. |
| `Rules::DIGITS` | `digits` | Numeric value with an exact number of digits. |
| `Rules::DIGITS_BETWEEN` | `digits_between` | Numeric value whose digit count is within a range. |
| `Rules::REGEX` | `regex` | Value matches a regular expression. |
| `Rules::DATE` | `date` | Valid date for a given format (default `Y-m-d`). |
| `Rules::AFTER` / `Rules::BEFORE` | `after` / `before` | Date after / before a `strtotime`-parsable bound. |
| `Rules::EMAIL` | `email` | Valid email address. |
| `Rules::URL` | `url` | Valid URL (optional scheme restriction). |
| `Rules::UUID` | `uuid` | Valid, non-nil UUID. |
| `Rules::JSON` | `json` | Valid JSON string. |
| `Rules::IP` / `Rules::IPV4` / `Rules::IPV6` | `ipv` / `ipv4` / `ipv6` | Valid IP address of the matching family. |
| `Rules::ALPHA` / `Rules::ALPHA_NUM` / `Rules::ALPHA_DASH` / `Rules::ALPHA_SPACES` | `alpha` / `alpha_num` / `alpha_dash` / `alpha_spaces` | Character-class constraints. |
| `Rules::SAME` / `Rules::DIFFERENT` | `same` / `different` | Value equals / differs from another field. |
| `Rules::STARTS_WITH` / `Rules::ENDS_WITH` | `starts_with` / `ends_with` | Value starts / ends with another field. |
| `Rules::REQUIRED_IF` / `Rules::REQUIRED_UNLESS` | `required_if` / `required_unless` | Conditional presence. |
| `Rules::REQUIRED_WITH` / `Rules::REQUIRED_WITH_ALL` | `required_with` / `required_with_all` | Presence tied to other fields. |
| `Rules::REQUIRED_WITHOUT` / `Rules::REQUIRED_WITHOUT_ALL` | `required_without` / `required_without_all` | Presence tied to the absence of other fields. |
| `Rules::REQUIRES` | `requires` | Other fields must be present and non-empty. |
| `Rules::PROHIBITED` / `Rules::PROHIBITED_IF` / `Rules::PROHIBITED_UNLESS` | `prohibited` / `prohibited_if` / `prohibited_unless` | Field is disallowed (conditionally). |
| `Rules::ACCEPTED` / `Rules::REJECTED` | `accepted` / `rejected` | Truthy / falsy acceptance values. |
| `Rules::PRESENT` | `present` | Field must be present, any value. |
| `Rules::DEFAULT` | `default` | Default value used when the field is absent. |
| `Rules::CALLBACK` | `callback` | Custom closure validation (array syntax only). |
| `Rules::ANY_OF` | `any` | All comma-separated values must be in the given set. |
| `Rules::ARRAY_MUST_HAVE_KEYS` / `Rules::ARRAY_CAN_ONLY_HAVE_KEYS` | `array_must_have_keys` / `array_can_only_have_keys` | Array key constraints. |
| `Rules::LOWERCASE` / `Rules::UPPERCASE` | `lowercase` / `uppercase` | Case constraints. |
| `Rules::EXTENSION` / `Rules::MIMES` | `extension` / `mimes` | File extension / MIME constraints. |

For the full, authoritative list of built-in rules and their parameters, see the [Somnambulist available rules](https://github.com/somnambulist-tech/validation?tab=readme-ov-file#available-rules) documentation.

## See also

- [Comparison & range rules](comparison.md) — `EqualRule`, `GreaterThanRule`, `LessThanRule`, `RangeRule`.
- [ISO 8601 rules](iso8601.md) — date, datetime, duration and combined rules.
- [Helpers](helpers.md) — the autoloaded rule-string functions.
- [Custom rules](custom-rules.md) — write your own rule on the provided abstracts.
- [Documentation index](README.md) — back to the table of contents.

<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;

/**
 * Generates the 'max:value' rule expression.
 *
 * The field under this rule must have a size less than or equal to the given number.
 *
 * Value size is calculated in the same way as the min rule.
 *
 * You can also validate the maximum size of uploaded files using this rule:
 * ```php
 * $validation = $validator->validate
 * ([
 *    'photo' => $_FILES['photo']
 * ],
 * [
 *    'photo' => 'required|max:2M'
 * ]);
 * ```
 *
 * @param string|int|float $value The maximum size (length, numeric value, or file size such as `2M`).
 *
 * @return string The compiled `max:<value>` rule expression.
 */
function max( string|int|float $value ) :string
{
   return Rules::MAX . Char::COLON . $value ;
}
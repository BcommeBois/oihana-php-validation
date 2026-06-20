<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'required_if:anotherField,value1,value2,...' rule expression.
 *
 * The field under this rule must be present and not empty if the another_field field is equal to any value.
 *
 * For example required_if:something,1,yes,on will be required if something's value is one of 1, '1', 'yes', or 'on'.
 *
 * @param string $anotherField The another field to evaluates.
 * @param mixed ...$values The values of `$anotherField` that make this field required.
 * @return string The compiled `required_if:<field>,<values>` rule expression.
 */
function requiredIf( string $anotherField , mixed ...$values ) :string
{
   return compile
   ([
       Rules::REQUIRED_IF ,
       compile( [ $anotherField , ...$values ] , Char::COMMA )
   ]
   , Char::COLON ) ;
}
<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'required_without:field1,field2,...' rule expression.
 *
 * The field under validation must be present and not empty only
 * when any of the other specified fields are not present.
 *
 * @param string ...$fields The other fields whose absence makes this field required.
 *
 * @return string The compiled `required_without:<fields>` rule expression.
 */
function requiredWithout( string ...$fields ) :string
{
   return compile
   ([
       Rules::REQUIRED_WITHOUT ,
       compile( $fields , Char::COMMA )
   ]
   , Char::COLON ) ;
}
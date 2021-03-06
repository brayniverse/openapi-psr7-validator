<?php
/**
 * @author Dmitry Lezhnev <lezhnev.work@gmail.com>
 * Date: 01 May 2019
 */
declare(strict_types=1);


namespace OpenAPIValidation\Schema\Keywords;


use OpenAPIValidation\Schema\Exception\ValidationKeywordFailed;
use Respect\Validation\Validator;

class MaxProperties extends BaseKeyword
{
    /**
     * The value of this keyword MUST be an integer.  This integer MUST be
     * greater than, or equal to, 0.
     *
     * An object instance is valid against "maxProperties" if its number of
     * properties is less than, or equal to, the value of this keyword.
     *
     * @param $data
     * @param int $maxProperties
     */
    public function validate($data, $maxProperties): void
    {
        try {
            Validator::arrayType()->assert($data);
            Validator::trueVal()->assert($maxProperties >= 0);

            if (count($data) > $maxProperties) {
                throw new \Exception(sprintf("The number of object's properties must be less or equal to %d", $maxProperties));
            }

        } catch (\Throwable $e) {
            throw ValidationKeywordFailed::fromKeyword("maxProperties", $data, $e->getMessage());
        }
    }
}
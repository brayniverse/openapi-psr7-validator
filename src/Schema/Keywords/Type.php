<?php
/**
 * @author Dmitry Lezhnev <lezhnev.work@gmail.com>
 * Date: 01 May 2019
 */
declare(strict_types=1);


namespace OpenAPIValidation\Schema\Keywords;


use OpenAPIValidation\Schema\Exception\FormatMismatch;
use OpenAPIValidation\Schema\Exception\ValidationKeywordFailed;
use OpenAPIValidation\Schema\TypeFormats\FormatsContainer;
use OpenAPIValidation\Schema\Utils\ArrayHelper;
use Respect\Validation\Validator;

class Type extends BaseKeyword
{
    /**
     * The value of this keyword MUST be either a string ONLY.
     *
     * String values MUST be one of the seven primitive types defined by the
     * core specification.
     *
     * An instance matches successfully if its primitive type is one of the
     * types defined by keyword.  Recall: "number" includes "integer".
     *
     * @param $data
     * @param string $type
     * @param string|null $format
     */
    public function validate($data, string $type, $format = null): void
    {
        try {
            Validator::in([
                0 => 'boolean',
                1 => 'object',
                2 => 'array',
                3 => 'number',
                4 => 'integer',
                5 => 'string',
                # Note that there is no null type; instead, the nullable attribute is used as a modifier of the base type.
            ])->stringType()->assert($type);

            if ($this->parentSchema->nullable && $data === null) {
                return;
            }

            switch ($type) {
                case "boolean":
                    if (!is_bool($data)) {
                        throw new \Exception(sprintf("Value '%s' is not a boolean", $data));
                    }
                    break;
                case "object":
                    if (!is_object($data) && !is_array($data)) {
                        throw new \Exception(sprintf("Value '%s' is not an object", $data));
                    }
                    break;
                case "array":
                    if (count($data) && !(is_array($data) && ArrayHelper::isAssoc($data))) {
                        throw new \Exception(sprintf("Value '%s' is not an array", $data));
                    }
                    if (!isset($this->parentSchema->items)) {
                        throw new \Exception(sprintf("items MUST be present if the type is array"));
                    }
                    break;
                case "number":
                    if (!is_numeric($data)) {
                        throw new \Exception(sprintf("Value '%s' is not a number", $data));
                    }
                    break;
                case "integer":
                    if (!is_int($data)) {
                        throw new \Exception(sprintf("Value '%s' is not an integer", $data));
                    }
                    break;
                case "string":
                    if (!is_string($data)) {
                        throw new \Exception(sprintf("Value '%s' is not a string", $data));
                    }
                    break;
                default:
                    throw new \Exception("Type '%s' is unexpected", $type);
            }

            // 2. Validate format now

            if (!$format) {
                return;
            }

            $formatValidator = FormatsContainer::getFormat($type, $format); # callable or FQCN
            if ($formatValidator === null) {
                return;
            }

            if (is_string($formatValidator) && !class_exists($formatValidator)) {
                throw new \RuntimeException(sprintf("'%s' does not loaded", $formatValidator));
            }

            if (is_string($formatValidator)) {
                $formatValidator = new $formatValidator;
            }

            if (!$formatValidator($data)) {
                throw FormatMismatch::fromFormat($format, $data);
            }

        } catch (\Throwable $e) {
            throw ValidationKeywordFailed::fromKeyword("type", $data, $e->getMessage(), $e);
        }
    }
}

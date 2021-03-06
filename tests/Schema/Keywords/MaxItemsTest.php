<?php
/**
 * @author Dmitry Lezhnev <lezhnev.work@gmail.com>
 * Date: 01 May 2019
 */
declare(strict_types=1);

namespace OpenAPIValidationTests\Schema\Keywords;

use OpenAPIValidation\Schema\Exception\ValidationKeywordFailed;
use OpenAPIValidation\Schema\Validator;
use OpenAPIValidationTests\Schema\SchemaValidatorTest;

class MaxItemsTest extends SchemaValidatorTest
{

    function test_it_validates_maxItems_green()
    {
        $spec = <<<SPEC
schema:
  type: array
  maxItems: 3
  items:
    type: number
SPEC;

        $schema = $this->loadRawSchema($spec);
        $data   = [1, 2, 3];

        (new Validator($schema, $data))->validate();
        $this->addToAssertionCount(1);
    }

    function test_it_validates_maxItems_red()
    {
        $spec = <<<SPEC
schema:
  type: array
  maxItems: 3
  items:
    type: number
SPEC;

        $schema = $this->loadRawSchema($spec);
        $data   = [1, 2, 3, 4];

        try {
            (new Validator($schema, $data))->validate();
        } catch (ValidationKeywordFailed $e) {
            $this->assertEquals('maxItems', $e->keyword());
        }
    }
}

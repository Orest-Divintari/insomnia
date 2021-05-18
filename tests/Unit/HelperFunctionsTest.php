<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelperFunctionsTest extends TestCase
{

    /** @test */
    public function it_transforms_snake_case_strings_to_camel_case()
    {
        $string = "this_is_a_string";

        $this->assertEquals('thisIsAString', snake_to_camel($string));
    }

    /** @test */
    public function it_transorms_snake_case_to_camel_case_and_capitalizes_the_first_letter()
    {
        $name = 'orestis_divintari';

        $this->assertEquals('OrestisDivintari', snake_to_camel($name, $capitalieFirstCharacter = true));
    }

    /** @test */
    public function it_converts_a_negative_value_to_false_boolean()
    {
        $negativeValues = collect([
            'off', 0, '0', false, 'false',
        ]);

        $this->assertTrue(
            $negativeValues->every(function ($key, $value) {
                return !to_bool($key);
            })
        );
    }

    /** @test */
    public function it_converts_a_positive_value_to_true_boolean()
    {
        $negativeValues = collect([
            'on', 1, '1', true, 'true',
        ]);

        $this->assertTrue(
            $negativeValues->every(function ($key, $value) {
                return to_bool($key);
            })
        );
    }
}
<?php

namespace Tests\Unit;

use App\Actions\StringToArrayAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StringToArrayActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function create_an_array_from_a_single_username()
    {
        $name = 'Orestis';

        $action = new StringToArrayAction($name);
        $namesArray = $action->execute();

        $this->assertCount(1, $namesArray);
        $this->assertEquals($namesArray[0], $name);
    }

    /** @test */
    public function create_an_array_of_names_from_comma_separated_string_of_names()
    {
        $names = "Orestis, John";

        $action = new StringToArrayAction($names);
        $namesArray = $action->execute();

        $this->assertCount(2, $namesArray);
        $this->assertContains('Orestis', $namesArray);
        $this->assertContains('John', $namesArray);
    }

    /** @test */
    public function createn_an_array_of_names_from_a_single_name_with_white_space()
    {
        $nameWithWhiteSpace = '      Orestis     ';
        $nameWithoutWhiteSpace = "Orestis";

        $action = new StringToArrayAction($nameWithWhiteSpace);
        $namesArray = $action->execute();

        $this->assertCount(1, $namesArray);
        $this->assertEquals($namesArray[0], $nameWithoutWhiteSpace);
    }

    /** @test */
    public function create_an_array_of_names_from_a_comma_separated_string_with_extra_white_space_around_names()
    {
        $orestis = 'Orestis';
        $john = 'John';
        $namesWithWhiteSpace = "   {$orestis}   ,    {$john}   ";

        $action = new StringToArrayAction($namesWithWhiteSpace);
        $namesArray = $action->execute();

        $this->assertCount(2, $namesArray);
        $this->assertContains($orestis, $namesArray);
        $this->assertContains($john, $namesArray);
    }
}
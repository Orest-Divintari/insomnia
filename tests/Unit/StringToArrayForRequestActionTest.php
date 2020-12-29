<?php

namespace Tests\Unit;

use App\Actions\StringToArrayForRequestAction;
use Illuminate\Http\Request;
use Tests\TestCase;

class StringToArrayForRequestActionTest extends TestCase
{
    protected $request;
    protected $attribute;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = app(Request::class);
        $this->attribute = 'usernames';
    }

    /** @test */
    public function create_an_array_of_names_from_a_single_name()
    {
        $name = 'George';
        $this->request->merge([$this->attribute => $name]);

        $action = new StringToArrayForRequestAction(
            $this->request,
            $this->attribute,
            $name
        );
        $action->execute();

        $this->assertIsArray(
            $this->request->input($this->attribute)
        );
        $this->assertEquals(
            $name,
            $this->request->input($this->attribute)[0]
        );
    }

    /** @test */
    public function createn_an_array_of_names_from_a_single_name_with_white_space()
    {

        $name = '  George  ';
        $this->request->merge([$this->attribute => $name]);

        $action = new StringToArrayForRequestAction(
            $this->request,
            $this->attribute,
            $name
        );
        $action->execute();

        $this->assertIsArray(request($this->attribute));
        $this->assertEquals(
            'George',
            $this->request->input($this->attribute)[0]
        );
    }

    /** @test */
    public function create_an_array_of_names_from_a_comma_separated_string()
    {
        $george = 'George';
        $john = 'John';
        $names = "{$george},{$john}";

        $this->request->merge([$this->attribute => $names]);

        $action = new StringToArrayForRequestAction(
            $this->request,
            $this->attribute,
            $names
        );
        $action->execute();

        $this->assertIsArray(request($this->attribute));
        $this->assertEquals(
            $george,
            $this->request->input($this->attribute)[0]
        );
        $this->assertEquals(
            $john,
            $this->request->input($this->attribute)[1]
        );
    }

    /** @test */
    public function create_an_array_of_names_from_a_comma_separated_string_with_extra_white_space_around_names()
    {
        $george = 'George';
        $john = 'John';
        $names = "   {$george},   {$john}    ";
        $this->request->merge([$this->attribute => $names]);

        $action = new StringToArrayForRequestAction(
            $this->request,
            $this->attribute,
            $names
        );
        $action->execute();

        $this->assertIsArray(request($this->attribute));
        $this->assertEquals(
            $george,
            $this->request->input($this->attribute)[0]
        );
        $this->assertEquals(
            $john,
            $this->request->input($this->attribute)[1]
        );
    }

    /** @test */
    public function do_nothing_when_the_input_is_not_string()
    {
        $names = ["array"];
        $this->request->merge([$this->attribute => $names]);

        $action = new StringToArrayForRequestAction(
            $this->request,
            $this->attribute,
            $names
        );
        $action->execute();

        $this->assertIsArray(request($this->attribute));
        $this->assertEquals(
            "array",
            $this->request->input($this->attribute)[0]
        );
    }
}
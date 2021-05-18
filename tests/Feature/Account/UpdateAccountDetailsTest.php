<?php

namespace Tests\Feature\Account;

use App\User\Details;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateAccountDetailsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function get_the_account_details_form()
    {
        $user = $this->signIn();

        $response = $this->get(route('account.details.edit'));

        $response->assertViewHas(compact('user'));
    }

    /** @test */
    public function a_user_can_update_the_details()
    {
        $user = $this->signIn();

        $details = $user->details;

        $details['location'] = 'netherlads';
        $details['website'] = 'insomnia';
        $details['birth_date'] = "1993-08-25";
        $details['occupation'] = 'developer';
        $details['gender'] = 'male';
        $details['about'] = 'My name is orestis and i am 28 years old.';
        $details['skype'] = 'orestis';
        $details['google_talk'] = 'orestis@gmail.com';
        $details['facebook'] = 'orestis uric';
        $details['twitter'] = 'OrestisDivintari';
        $details['instagram'] = 'Orestis';
        $details['location'] = 'netherlads';

        $response = $this->patch(route('account.details.update', $details));

        $response->assertSessionHasNoErrors();
        $this->assertTrue(
            collect($user->details)
                ->every(function ($value, $key) use ($details) {
                    return $value == $details[$key];
                })
        );
    }

    /** @test */
    public function the_birth_date_must_be_of_type_date_in_Y_m_d_format()
    {
        $user = $this->signIn();
        $birth_date = "25-08-1993";

        $response = $this->patch(route('account.details.update', compact('birth_date')));

        $response->assertSessionHasErrors('birth_date');
    }

    /** @test */
    public function the_gender_must_be_male_or_female_if_specified()
    {
        $user = $this->signIn();
        $gender = ['random'];

        $response = $this->patch(route('account.details.update', compact('gender')));

        $response->assertSessionHasErrors('gender');
    }
}
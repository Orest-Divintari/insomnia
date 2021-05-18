<?php

namespace App\Http\Requests;

use App\User\Privacy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserPrivacyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'show_current_activity' => ['sometimes', 'required', 'accepted'],
            'show_birth_date' => ['sometimes', 'required', 'accepted'],
            'show_birth_year' => ['sometimes', 'required', 'accepted'],
            'show_details' => [
                'required',
                'string',
                Rule::in([
                    Privacy::ALLOW_NOONE,
                    Privacy::ALLOW_MEMBERS,
                    Privacy::ALLOW_FOLLOWING,
                ]),
            ],
            'post_on_profile' => [
                'required',
                'string',
                Rule::in([
                    Privacy::ALLOW_NOONE,
                    Privacy::ALLOW_MEMBERS,
                    Privacy::ALLOW_FOLLOWING,
                ]),
            ],
            'start_conversation' => [
                'required',
                'string',
                Rule::in([
                    Privacy::ALLOW_NOONE,
                    Privacy::ALLOW_MEMBERS,
                    Privacy::ALLOW_FOLLOWING,
                ]),
            ],
            'show_identities' => [
                'string',
                'required',
                Rule::in([
                    Privacy::ALLOW_NOONE,
                    Privacy::ALLOW_MEMBERS,
                    Privacy::ALLOW_FOLLOWING,
                ]),
            ],
        ];
    }

    /**
     * Persist changes
     *
     * @return void
     */
    public function persist()
    {
        $attributes = collect([
            'show_current_activity',
            'show_birth_date',
            'show_birth_year',
        ]);

        $attributes->each(function ($attribute) {
            if ($this->missing($attribute)) {
                $this->merge([$attribute => false]);
            }
        });

        $this->user()->privacy()->merge($this->input());
    }
}
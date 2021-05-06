<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccountDetailsRequest extends FormRequest
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
            'location' => ['nullable', 'string'],
            'birth_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'website' => ['nullable', 'string'],
            'gender' => ['nullable', 'string', Rule::in(['male', 'female', ''])],
            'occupation' => ['nullable', 'string'],
            'about' => ['nullable', 'string'],
            'skype' => ['nullable', 'string'],
            'google_talk ' => ['nullable', 'string'],
            'facebook' => ['nullable', 'string'],
            'twitter' => ['nullable', 'string'],
            'instagram' => ['nullable', 'string'],
        ];
    }

    public function persist()
    {
        $this->user()->details()->merge($this->input());
    }
}
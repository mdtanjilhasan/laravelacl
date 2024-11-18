<?php

namespace Modules\Acl\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Acl\Traits\HttpExceptionsTrait;

class UserSoftDeleteValidation extends FormRequest
{
    use HttpExceptionsTrait;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'type' => [
                'bail',
                'required',
                function ($attribute, $value, $fail) {
                    if ( ! in_array( strtolower($value), ['trash', 'delete', 'restore'] ) ) {
                        $fail("The $attribute is invalid.");
                    }
                }
            ]
        ];

        if (is_array(request('id'))) {
            $rules['id'] = 'bail|required|array';
            $rules['id.*'] = 'bail|required|integer|exists:users,id';
        } else {
            $rules['id'] = 'bail|required|integer|exists:users,id';
        }

        return $rules;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->fail($validator->errors()->first(), $validator->errors(),  422));
    }
}

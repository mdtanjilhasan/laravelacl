<?php

namespace Modules\Acl\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Acl\Traits\HttpExceptionsTrait;

class ProfileValidation extends FormRequest
{
    use HttpExceptionsTrait;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'image' => 'sometimes|required|image'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->fail($validator->errors()->first(), $validator->errors(),  422));
    }
}

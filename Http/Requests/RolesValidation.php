<?php

namespace Modules\Acl\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Acl\Traits\HttpExceptionsTrait;

class RolesValidation extends FormRequest
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
        return [
            'name' => 'bail|required|string',
            'id' => 'sometimes|required|integer|exists:roles,id'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->fail($validator->errors()->first(), $validator->errors(),  422));
    }
}

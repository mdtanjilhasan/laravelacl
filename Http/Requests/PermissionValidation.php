<?php

namespace Modules\Acl\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Acl\Traits\HttpExceptionsTrait;

class PermissionValidation extends FormRequest
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
            'name' => request()->has('id') ? ['required', 'string', Rule::unique('permissions')->ignore(request('id'))] : 'required|string|unique:permissions,name',
            'permission_group_id' => 'required|integer|exists:permission_groups,id',
            'id' => 'sometimes|required|integer|exists:permissions,id'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->fail($validator->errors()->first(), $validator->errors(),  422));
    }
}

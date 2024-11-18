<?php

namespace Modules\Acl\Http\Requests;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Acl\Traits\HttpExceptionsTrait;

class UsersValidation extends FormRequest
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
            'first_name' => 'bail|required|string',
            'last_name' => 'bail|required|string',
            'email' => request()->has('user_id') ? ['bail', 'required', 'email', Rule::unique('users')->ignore(request('user_id'))] : 'bail|required|email|unique:users,email',
            'role' => 'bail|required|integer|exists:roles,id',
            'password' => 'bail|sometimes|required|min:8',
            'user_id' => 'sometimes|required|integer|exists:users,id'
        ];
    }

    public function getUserData(): array
    {
        $data = $this->validated();
        $user = ['name' => "{$data['first_name']} {$data['last_name']}", 'email' => $data['email'], 'email_verified_at' => Carbon::now(), 'remember_token' => Str::random(10)];

        if (!array_key_exists('password', $data)) {
            $user['password'] = Hash::make('*JsZ*iW5Y200');
        } else {
            $user['password'] = Hash::make($data['password']);
        }

        if (array_key_exists('user_id', $data)) {
            $user['user_id'] = $data['user_id'];
            if (!array_key_exists('password', $data)) {
                unset($user['password']);
            } else {
                $user['password'] = Hash::make($data['password']);
            }
        }

        $profile = ['first_name' => $data['first_name'], 'last_name' => $data['last_name'], 'role_id' => $data['role']];

        return ['user' => $user, 'profile' => $profile];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->fail($validator->errors()->first(), $validator->errors(),  422));
    }
}

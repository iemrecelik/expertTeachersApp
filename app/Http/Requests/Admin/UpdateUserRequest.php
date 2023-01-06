<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
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
     * @return array
     */
    public function rules()
    {
        return [
            'name'  => 'required|string',
            'email'  => 'required|email',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Kullanıcı ismi zorunludur.',
            'name.string' => 'Kullanıcı ismi sadece rakamlardan oluşamaz.',
            'email.required' => 'Email adresi zorunludur.',
            'email.string' => 'Email adresi doğru formatta girilmelidir.',
        ];
    }
}

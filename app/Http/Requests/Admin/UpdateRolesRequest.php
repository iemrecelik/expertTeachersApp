<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRolesRequest extends FormRequest
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
            'nickname'  => 'required|string',
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
            'nickname.required' => 'Rol takma isim zorunludur.',
            'nickname.string' => 'Rol takma ismi sadece rakamlardan oluşamaz.',
        ];
    }
}

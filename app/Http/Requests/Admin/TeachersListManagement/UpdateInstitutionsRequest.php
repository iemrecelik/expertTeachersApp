<?php

namespace App\Http\Requests\Admin\TeachersListManagement;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInstitutionsRequest extends FormRequest
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
            'inst_name'  => 'required|string',
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
            'inst_name.required' => 'Kategori ismi zorunludur.',
            'inst_name.string' => 'Kategori ismi sadece rakamlardan oluşamaz.',
        ];
    }
}

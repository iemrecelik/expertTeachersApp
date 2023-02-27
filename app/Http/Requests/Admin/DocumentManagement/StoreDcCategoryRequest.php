<?php

namespace App\Http\Requests\Admin\DocumentManagement;

use Illuminate\Foundation\Http\FormRequest;

class StoreDcCategoryRequest extends FormRequest
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
            'dc_cat_name'  => 'required|string',
            'dc_cat_id'  => 'required|integer',
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
            'dc_cat_name.required' => 'Kategori ismi zorunludur.',
            'dc_cat_name.string' => 'Kategori ismi sadece rakamlardan oluşamaz.',
        ];
    }
}

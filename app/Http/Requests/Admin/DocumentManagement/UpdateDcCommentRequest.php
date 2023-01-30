<?php

namespace App\Http\Requests\Admin\DocumentManagement;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDcCommentRequest extends FormRequest
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
            'dc_com_text'  => 'required|string',
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
            'dc_com_text.required' => 'Kategori ismi zorunludur.',
            'dc_com_text.string' => 'Kategori ismi sadece rakamlardan oluşamaz.',
        ];
    }
}

<?php

namespace App\Http\Requests\Admin\LawsuitsManagement;

use Illuminate\Foundation\Http\FormRequest;

class StoreLawsuitsRequest extends FormRequest
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
            'law_brief'  => 'required|string',
            'dc_id'  => 'required|integer|unique:lawsuits,dc_id',
            'thr_id'  => 'required_without:uns_id|integer',
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
            'dc_id.required' => 'Evrak numarası zorunludur.',
            'dc_id.integer' => 'Evrak numarası sadece rakamlardan oluşamaz.',
            'law_brief.required' => 'Dava kısa açıklaması zorunludur.',
            'law_brief.string' => 'Dava kısa açıklaması sadece rakamlardan oluşamaz.',
            'thr_id.required_without' => 'İlgili Öğretmeni yada sendikayı giriniz. ',
        ];
    }
}

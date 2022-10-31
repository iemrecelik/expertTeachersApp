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
            'law_brief'  => 'required|string|max:200',
            'dc_id'  => 'required|integer|unique:lawsuits,dc_id',
            'thr_id'  => 'required_without:uns_id|integer',
            /* 'sub_description'  => 'nullable',
            'sub_description.*'  => 'string|min:10', */
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
            'dc_id.unique' => 'Evrak numarası daha önce kaydedilmiş.',
            'dc_id.integer' => 'Evrak numarası sadece rakamlardan oluşamaz.',
            'law_brief.required' => 'Dava kısa açıklaması zorunludur.',
            'law_brief.string' => 'Davanın kısa açıklaması sadece rakamlardan oluşamaz.',
            'law_brief.max' => 'Davanın kısa açıklaması en fazla 200 karakterli olmalıdır.',
            'thr_id.required_without' => 'İlgili Öğretmeni yada sendikayı giriniz. ',
            /* 'sub_description.*.min' => ':attribute Lütfen Maddeleri en 10 karakter olacak şekilde yazınız.',
            'sub_description.*.string' => ':attribute Lütfen Maddeleri en 10 karakter olacak şekilde yazınız.', */
        ];
    }
}

<?php

namespace App\Http\Requests\Admin\DocumentManagement;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRecordCountRequest extends FormRequest
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
            'user_id.*'  => 'required|integer',
            'rp_count.*'  => 'required|string',
            'rp_date'  => 'required|integer',
            'rp_date'  => 'required|date',
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
            'user_id.*.required' => 'Kullanıcı biligisi zorunludur.',
            'user_id.*.integer' => 'Kullanıcı biligisi sayı tipinde olması gerekiyor.',
            'rp_count.*.required' => 'evrak sayısı zorunludur.',
            'rp_count.*.integer' => 'evrak sayısı sayı tipinde olması gerekiyor.',
            'rp_date.required' => 'Tarih girmek zorunludur.',
            'rp_date.date' => 'Tarih alanı tarih formatında olmak zorundadır.',
        ];
    }
}

<?php

namespace App\Http\Requests\Admin\OldRegulation;

use Illuminate\Foundation\Http\FormRequest;

class SearchListRequest extends FormRequest
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
            'tc_num'    => 'required_without_all:name,surname|digits:11',
            'name'      => 'regex:/^[a-zA-Z ]+$/|nullable',
            'surname'   => 'regex:/^[a-zA-Z ]+$/|nullable',
            /* 'name'   => 'required_without_all:tc_num,surname',
            'surname'   => 'required_without_all:tc_num,name', */
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
            'tc_num.required_without_all' => 'Tc Kimlik numrası, isim veya soyisim den birini girmelisiniz.',
            /* 'name.required_without_all' => 'Tc Kimlik numrası, isim veya soyisim den birini girmelisiniz.',
            'surname.required_without_all' => 'Tc Kimlik numrası, isim veya soyisim den birini girmelisiniz.', */
            'tc_num.digits' => 'Tc Kimlik numrasını yanlış girdiniz.',
            'name.regex' => 'Lütfen ismi sadece harf giriniz.',
            'surname.regex' => 'Lütfen soyismi sadece harf giriniz.',
        ];
    }
}

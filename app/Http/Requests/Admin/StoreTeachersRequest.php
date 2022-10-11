<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeachersRequest extends FormRequest
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
            'thr_tc_no' => 'required|digits:11',
            'thr_name' => 'required|regex:/^[a-zA-ZğüşöçıİĞÜŞÖÇ ]+$/u',
            'thr_surname' => 'required|regex:/^[a-zA-ZğüşöçıİĞÜŞÖÇ ]+$/u',
            'thr_career_ladder' => 'required|regex:/^[a-zA-ZğüşöçıİĞÜŞÖÇ ]+$/u',
            /* 'thr_degree' => 'required|string',
            'thr_task' => 'required|string',
            'thr_education_st' => 'required|string',
            'thr_phone' => 'required|integer',
            'thr_place_of_task' => 'required|string', */
            'inst_id' => 'required|integer',
            'thr_gender' => 'required|in:0,1',
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
            'thr_tc_no.required' => 'Tc alanı zorunludur.',
            'thr_tc_no.digit' => 'Tc alanı sadece rakamlardan ve 11 tane olmalıdır.',
            'thr_name.required' => 'İsim alanı zorunludur.',
            'thr_name.regex' => 'İsim alanı sadece harflerden oluşmalıdır.',
            'thr_surname.required' => 'Soy isim alanı zorunludur.',
            'thr_surname.regex' => 'Soy isim alanı sadece harflerden oluşmalıdır.',
            'thr_career_ladder.required' => 'Kariyer basamağı alanı zorunludur.',
            'thr_career_ladder.regex' => 'Kariyer basamağı alanı sadece harflerden oluşmalıdır.',
            'inst_id.required' => 'Kurum alanı zorunludur.',
            'thr_gender.required' => 'Cinsiyet alanı zorunludur.',
            'thr_gender.in' => 'Cinsiyet sadece erkek veya bayan olmalıdır.',
        ];
    }
}

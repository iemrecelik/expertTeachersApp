<?php

namespace App\Http\Requests\Admin\DocumentManagement;

use Illuminate\Foundation\Http\FormRequest;

class StoreDcDocumentsRequest extends FormRequest
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
            'dc_cat_id'                 => 'required|integer|notIn:0',
            'dc_sender_file'            => 'required|file',
            'dc_sender_attach_files.*'  => 'file',
            'dc_who_send'               => 'required|string',
            'dc_who_receiver'           => 'required|string',
            'dc_base_number'            => 'regex:/^20[0-9]{2}/[0-9]*$/',
            'dc_number'                 => 'required|integer',
            'dc_subject'                => 'required|string',
            'dc_content'                => 'required|string',
            'dc_raw_content'            => 'string',
            'dc_date'                   => 'required|date',
            'rel_sender_file.*'         => 'required|file',
            'rel_sender_attach_files.*.*'  => 'file',
            'rel_dc_who_send.*'         => 'required|string',
            'rel_dc_number.*'           => 'required|integer',
            'rel_dc_subject.*'          => 'required|string',
            'rel_dc_content.*'          => 'required|string',
            'rel_dc_date.*'             => 'required|date',
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
            'dc_cat_id.required'            => 'Lütfen kategori seçiniz.',
            'dc_cat_id.not_in'              => 'Lütfen kategori seçiniz.',
            'dc_sender_file.required'       => 'Yüklenecek evrağın udf dosyasını yükleyiniz.',
            'dc_sender_attach_files.*.file' => 'Lütfen sadece dosya yükleyiniz.',
            'dc_who_send.required'          => 'Gönderici bilgilerini giriniz.',
            'dc_who_send.string'            => 'Gönderici bilgilerini sadece rakam yada harf haricinde karakter girmeyiniz.',
            'dc_who_receiver.required'      => 'Alıcı bilgilerini giriniz.',
            'dc_who_receiver.string'        => 'Alıcı bilgilerini sadece rakam yada harf haricinde karakter girmeyiniz.',
            'dc_base_number.regex'          => 'Esas Numarayı (20**/****) formata uygun girmediniz.',
            'dc_number.required'            => 'Evrak sayını giriniz.',
            'dc_number.integer'             => 'Sadece rakam giriniz.',
            'dc_subject.required'           => 'Evrak konusunu giriniz.',
            'dc_subject.string'             => 'Evrak konusunu sadece harf haricinde karakter girmeyiniz.',
            'dc_content.required'           => 'Evrak içeriğini giriniz.',
            'dc_content.string'             => 'Evrak içeriğini harf haricinde karakter girmeyiniz.',
            'dc_raw_content.required'       => 'Evrak içeriğini giriniz.',
            'dc_raw_content.string'         => 'Evrak içeriğini harf haricinde karakter girmeyiniz.',
            'dc_date.required'              => 'Evrak tarihini giriniz.',
            'dc_date.date'                  => 'Evrak tarihi formatı:"xx.xx.xxxx" şeklinde olmalıdır. ',
            'rel_sender_file.*.required'    => 'Yüklenecek ilgili evrağın udf dosyasını yükleyiniz.',
            'rel_sender_file.*.file'        => 'Sadece udf dosyası yükleyiniz.',
            'rel_sender_attach_files.*.*.file'   => 'sadece dosya yükleyiniz.',
            'rel_dc_who_send.*.required'         => 'İlgili evrağın gönderici bilgilerini giriniz.',
            'rel_dc_who_send.*.string'      => 'İlgili evrağın gönderici bilgilerini sadece rakam yada harf haricinde karakter girmeyiniz.',
            'rel_dc_number.*.required'      => 'İlgili evrak sayını giriniz.',
            'rel_dc_number.*.integer'       => 'İlgili evrağa sadece rakam giriniz.',
            'rel_dc_subject.*.required'     => 'İlgili evrak konusunu giriniz.',
            'rel_dc_subject.*.string'       => 'İlgili evrak konusunu sadece harf haricinde karakter girmeyiniz.',
            'rel_dc_date.*.required'        => 'İlgili evrak tarihini giriniz.',
            'rel_dc_date.*.date'            => 'İlgili evrak tarihi formatı:"xx.xx.xxxx" şeklinde olmalıdır. ',
            'rel_dc_content.required'       => 'ilgi Evrağın içeriğini giriniz.',
            'rel_dc_content.string'         => 'ilgi Evrağın harf haricinde karakter girmeyiniz.',
        ];
    }
}

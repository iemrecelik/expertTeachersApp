<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidateUploadFileExt implements Rule
{
    private string $fileName;
    private string $ext;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $ext = pathinfo($value->getClientOriginalName(), PATHINFO_EXTENSION);

        $this->ext = $ext;
        $this->fileName = $value->getClientOriginalName();

        $co = \App\Models\Admin\Settings::whereRaw(
            'set_allow_file_ext_names LIKE :ext', 
            ['ext' => '%'.$this->ext.'%']
        )->count();

        return $co > 0 ? true : false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "$this->fileName dosyasının (.$this->ext) formatı desteklenmiyor. Eklemek istiyorsanız. Yöneticinize danışınız.";
    }
}

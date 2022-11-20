<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidateTCNo implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(
        string $name, 
        string $surname,
        int $birthYear
    )
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
        $client = new \SoapClient('https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx?WSDL');

        $response = $client->TCKimlikNoDogrula([
            'TCKimlikNo' => $value,
            'Ad' => \Transliterator::create("tr-Upper")->transliterate($this->name),
            'Soyad' => \Transliterator::create("tr-Upper")->transliterate($this->surname),
            'DogumYili' => $this->birthYear
        ]);

        return $response->TCKimlikNoDogrulaResult;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}

<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidateMathTCNo implements Rule
{
    public string $name;
    public string $surname;
    public int $birthYear;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {}

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->tckimlik($value) ?? false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Geçerli bir Tc Numarası giriniz.';
    }

    private function tckimlik($tckimlik){
        $olmaz=array('11111111110','22222222220','33333333330','44444444440','55555555550','66666666660','7777777770','88888888880','99999999990'); 
        
        $ilkt = 0;
        $sont = 0;
        $tumt = 0;

        if($tckimlik[0]==0 or strlen($tckimlik)!=11){ return false;  } 
        else{
            for($a=0;$a<9;$a=$a+2){ $ilkt=$ilkt+$tckimlik[$a]; } 
            for($a=1;$a<9;$a=$a+2){ $sont=$sont+$tckimlik[$a]; } 
            for($a=0;$a<10;$a=$a+1){ $tumt=$tumt+$tckimlik[$a]; } 
            if(($ilkt*7-$sont)%10!=$tckimlik[9] or $tumt%10!=$tckimlik[10]){ return false; } 
            else{  
                foreach($olmaz as $olurmu){ if($tckimlik==$olurmu){ return false; } } 
                return true;
            } 
        } 
    }
}

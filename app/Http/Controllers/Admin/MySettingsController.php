<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MySettingsController extends Controller
{
    private function encryption($text)
    {
        // Store a string into the variable which
        // need to be Encrypted
        $simple_string = $text;
        
        // Store the cipher method
        $ciphering = "AES-128-CTR";
        
        // Use OpenSSl Encryption method
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;
        
        // Non-NULL Initialization Vector for encryption
        $encryption_iv = '1029384756123456';
        
        // Store the encryption key
        $encryption_key = "kariyer_basamaklari";
        
        // Use openssl_encrypt() function to encrypt the data
        $encryption = openssl_encrypt($simple_string, $ciphering,
                    $encryption_key, $options, $encryption_iv);

        return $encryption;
    }

    private function decryption($encryption)
    {   
        // Store the cipher method
        $ciphering = "AES-128-CTR";
        
        // Use OpenSSl encryption method
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;

        // Non-NULL Initialization Vector for decryption
        $decryption_iv = '1029384756123456';
        
        // Store the decryption key
        $decryption_key = "kariyer_basamaklari";
        
        // Use openssl_decrypt() function to decrypt the data
        $decryption = openssl_decrypt ($encryption, $ciphering, 
                $decryption_key, $options, $decryption_iv);

        return $decryption;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /* echo '<pre>';
        $enc = $this->encryption('1233214');
        var_dump($enc);
        var_dump($this->decryption($enc));
        die; */

        $user = auth()->user();

        return view(
            'admin.my_settings.index',
            ['user' => $user]
        );
    }

    public function update(Request $request)
    {
        $request->validate(
            [
                'password' => 'required',
                'name' => 'required',
                'email' => 'required',
            ],
            [
                'password.required' => 'Değişikleri kaydedebilmeniz için şifrenizi girmeniz gerekmektedir.',
                'name.required' => 'Kullanıcı adınızı boş bırakmayınız.',
                'email.required' => 'Email adresinizi boş bırakmayınız.'
            ]
        );

        $user = auth()->user();
        $params = $request->all();

        if (Hash::check($params['password'], $user->password)) {
            $updParams = [
                'name' => $params['name'],
                'email' => $params['email'],
            ];

            if(isset($params['new_password'])) {
                $updParams['password'] = Hash::make($params['new_password']);
            }
            
            if(isset($params['mebbis_name'])) {
                $updParams['mebbis_name'] = $params['mebbis_name'];
            }

            if(isset($params['mebbis_password'])) {
                $updParams['mebbis_password'] = $this->encryption($params['mebbis_password']);
            }

            User::where('id', $user->id)->update($updParams);
        }else {
            throw ValidationException::withMessages(
                ['password' => 'Yanlış şifre girdiniz. Lütfen tekrar şifrenizi girniz.']
            );
        }

        $msg = ['succeed' => __('messages.edit_success')];

        return redirect()->route('admin.mySettings.index')
            ->with($msg);
    }
}

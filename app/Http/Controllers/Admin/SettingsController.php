<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Admin\Settings;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $setting = Settings::find(1);
        $signatureNames = explode('|', $setting->set_raw_auth_signature_names);
        $ipNames = explode('|', $setting->set_ip_names);
        $allowFileExt = explode('|', $setting->set_allow_file_ext);

        $ipNames = array_map(function($name) {
            return [
                'label' => $name,
                'id' => $name
            ];
        }, $ipNames);
        
        $signatureNames = array_map(function($name) {
            return [
                'label' => $name,
                'id' => $name
            ];
        }, $signatureNames);

        $allowFileExt = array_map(function($name) {
            return [
                'label' => $name,
                'id' => $name
            ];
        }, $allowFileExt);

        return view('admin.settings.index', [
            'ipNames' => [
                'arr' => $ipNames,
                'val' => array_column($ipNames, 'id')
            ],

            'signatureNames' => [
                'arr' => $signatureNames,
                'val' => array_column($signatureNames, 'id')
            ],

            'allowFileExt' => [
                'arr' => $allowFileExt,
                'val' => array_column($allowFileExt, 'id')
            ],
        ]);
    }

    public function update(UpdateSettingsRequest $request)
    {
        // dd($request->all());
        $params['set_auth_signature_names'] = $request->input('set_auth_signature_names');
        $params['set_ip_names'] = $request->input('set_ip_names');


        $rawAuthSignatureNames = [];
        $setAuthSignatureNames = [];
        foreach ($params['set_auth_signature_names'] as $signatureKey => $signatureVal) {
            $setAuthSignatureNames[] = \Transliterator::create('tr-lower')->transliterate($signatureVal);
            $setAuthSignatureNames[] = \Transliterator::create('tr-title')->transliterate($signatureVal);
            $setAuthSignatureNames[] = \Transliterator::create('tr-upper')->transliterate($signatureVal);

            $rawAuthSignatureNames[] = \Transliterator::create('tr-title')->transliterate($signatureVal);
        }

        $rawAuthSignatureNames = implode('|', $rawAuthSignatureNames);
        $setAuthSignatureNames = implode('|', $setAuthSignatureNames);
        $setIpNames = implode('|', $params['set_ip_names']);

        $settings = Settings::find(1);

        if(isset($settings)) {
            Settings::where('id', 1)
                ->update([
                    'set_raw_auth_signature_names' => $rawAuthSignatureNames,
                    'set_auth_signature_names' => $setAuthSignatureNames,
                    'set_ip_names' => $setIpNames,
                ]);
        }else {
            Settings::create([
                'set_raw_auth_signature_names' => $rawAuthSignatureNames,
                'set_auth_signature_names' => $setAuthSignatureNames,
                'set_ip_names' => $setIpNames,
            ]);
        }

        $msg = ['succeed' => __('messages.edit_success')];
        
        return redirect()->route('admin.settings.index')
                        ->with($msg);
    }
}

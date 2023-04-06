<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Admin\Settings;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $ip = \Request::ip();

        $logInfo = new \App\Library\LogInfo();

        if($ip !== "::1" && $ip !== "127.0.0.1" && $ip !== "10.8.41.54") {
            $logInfo->crShowLog(
                $ip.' ip numarasıyla sayfaya istekte bulunuldu.', 
                'logs/guest/requestIp/'.str_replace('.', '_', $ip).'.log'
            );
            
            abort(403, "SİTE BAKIMDADIR. KISA SÜRE İÇİNDE AÇILACAK.");
        }

        if($ip !== "::1" && $ip !== "10.8.41.54" && $ip !== "127.0.0.1") {
            $settings = Settings::whereRaw('set_ip_names LIKE ?', ['%'.$ip.'%'])
                    ->count();

            if($settings < 1) {
                abort(403, "İp ({$ip}) Numaranız Tanımlı Değil Lütfen Yetkili Tarafından Ekletiniz.");

                $logInfo->crShowLog($ip.' izinsiz ip numarasıyla sayfaya istekte bulunuldu.', 'logs/guest/unauthorizedRequIp.log');
            }
        }
    }
}

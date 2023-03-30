<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogFailedLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Failed  $event
     * @return void
     */
    public function handle(Failed $event)
    {
        $ip = \Request::ip();

        $logInfo = new \App\Library\LogInfo();
        $logInfo->crShowLog(
            $ip.' ip numaralı kullanıcı başarısız giriş denemesinde bulundu.', 
            'logs/guest/requestIp/'.str_replace('.', '_', $ip).'.log'
        );
    }
}

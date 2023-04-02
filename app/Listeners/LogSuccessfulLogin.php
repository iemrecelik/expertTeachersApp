<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSuccessfulLogin
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
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $this->loginLog($event);
    }

    private function loginLog($event)
    {
        $ip = \Request::ip();
        $nameAndEmail = $event->user->name.'('.$event->user->email.')';

        $logInfo = new \App\Library\LogInfo();
        $logInfo->crShowLog(
            "$ip ip numarasıyla $nameAndEmail adlı kullanıcı giriş işlemini gerçekleştirdi.",
            'logs/login/'.date('d.m.Y').'-login.log'
        );
    }
}

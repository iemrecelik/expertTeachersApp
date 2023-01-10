<?php

namespace App\Library;

use \Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * log Info Proccess
 */
class LogInfo
{
    private $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function crShowLog($message)
    {
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/'.date('d_m_Y').'/'.$this->user->email.'.log'),
        ])->info($message);
    }

    public function crCreateLog($model)
    {
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/'.date('d_m_Y').'/'.$this->user->email.'.log'),
        ])->info(
            'create::'
            .json_encode($model, JSON_UNESCAPED_UNICODE)
            .' verileri eklendi.'
        );
    }
    
    public function crUpdateLog($oldModel, $model)
    {
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/'.date('d_m_Y').'/'.$this->user->email.'.log'),
        ])->info(
            'update::'
            .json_encode($model, JSON_UNESCAPED_UNICODE)
            .' verileri '
            .json_encode($oldModel, JSON_UNESCAPED_UNICODE)
            .' verileri ile değiştirildi.'
        );
    }

    public function crDestroyLog($model)
    {
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/'.date('d_m_Y').'/'.$this->user->email.'.log'),
        ])->info(
            'destroy::'
            .json_encode($model, JSON_UNESCAPED_UNICODE)
            .' verileri silindi.'
        );
    }
}
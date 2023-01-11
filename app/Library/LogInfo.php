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
    private $moduleName;

    public function __construct($moduleName)
    {
        $this->user = Auth::user();
        $this->moduleName = $moduleName;
    }

    public function crShowLog($message)
    {
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/users/'.strtotime(date('d.m.Y')).'/'.$this->user->email.'.log'),
        ])->info($message);
    }

    public function crCreateLog($model)
    {
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/users/'.strtotime(date('d.m.Y')).'/'.$this->user->email.'.log'),
        ])->info(
            'Ekleme::'
            .$this->moduleName.'::'
            .json_encode($model, JSON_UNESCAPED_UNICODE)
            .' <b>verileri eklendi.</b>'
        );
    }
    
    public function crUpdateLog($oldModel, $model)
    {
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/users/'.strtotime(date('d.m.Y')).'/'.$this->user->email.'.log'),
        ])->info(
            'Düzenleme::'
            .$this->moduleName.'::'
            .json_encode($model, JSON_UNESCAPED_UNICODE)
            .' <b>verileri</b> '
            .json_encode($oldModel, JSON_UNESCAPED_UNICODE)
            .' <b>verileri ile değiştirildi.</b>'
        );
    }

    public function crDestroyLog($model)
    {
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/users/'.strtotime(date('d.m.Y')).'/'.$this->user->email.'.log'),
        ])->info(
            'Silme::'
            .$this->moduleName.'::'
            .json_encode($model, JSON_UNESCAPED_UNICODE)
            .' <b>verileri silindi.</b>'
        );
    }
}
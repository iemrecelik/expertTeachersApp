<?php

namespace App\ModelsRepository\Admin;
use App\ModelsRepository\GlobalRepository;

trait DcDocumentsRepository
{
    use GlobalRepository;
    
    public static function boot()
    {
        parent::boot();
        
        static::creating(function($model)
        {
            $user = auth()->user();
            $date = date("d-m-Y H:i:s");

            $model->created_by = $user->id;
            $model->created_by_name = "$date | $user->name ($user->email)";
        });

        /* static::updating(function($model)
        {
            $user = auth()->user();
            $date = date("d-m-Y H:i:s");

            $model->updated_by = $user->id;
            $model->updated_by_name = "$date | $user->name ($user->email)";
        }); */
    }
}

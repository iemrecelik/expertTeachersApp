<?php

namespace App\ModelsRepository\Admin;
use App\ModelsRepository\GlobalRepository;

trait LawsuitsRepository
{
    use GlobalRepository;
    
    //Repository content...
    public static function boot()
    {
        parent::boot();
        static::creating(function($model)
        {
            $user = auth()->user();
            $date = date("d-m-Y H:i:s");

            $model->created_by = $user->id;
            $model->created_by_name = "$date | $user->name ($user->email)";

            $model->updated_by = $user->id;
            $model->updated_by_name = null;
        });
        static::updating(function($model)
        {
            $user = auth()->user();
            $date = date("d-m-Y H:i:s");

            $model->updated_by = $user->id;
            $model->updated_by_name = "$date | $user->name ($user->email)";
        });
    }
}

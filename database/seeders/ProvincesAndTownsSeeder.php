<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvincesAndTownsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* php artisan db:seed --class=ProvincesAndTownsSeeder */
        $datas = config('constants.provincesAndTowns');

        foreach ($datas as $key => $val) {
            $id = DB::table('provinces')->where('prv_name', $key)->first();

            if(!$id) {
                $id = DB::table('provinces')->insertGetId([
                    'prv_name' => $key,
                ]);
            }else {
                $id = $id->id; 
            }

            foreach ($val['districts'] as $k => $v) {
                $tid = DB::table('towns')->where([
                    ['twn_name', '=', $v],
                    ['prv_id', '=', $id],
                ])->first();

                if(!$tid) {
                    DB::table('towns')->insert([
                        'twn_name' => $v,
                        'prv_id' => $id
                    ]);
                }
            }
        }
    }
}

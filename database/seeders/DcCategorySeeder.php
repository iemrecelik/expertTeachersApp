<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\DcCategory;

class DcCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            'Aday Öğretmen',
            'Uzman Öğretmen',
            'Baş Öğretmen',
            'Davalar',
            'Soru Önergesi',
            'Olur',
        ];

        foreach ($datas as $key => $val) {
            if($val !== 'Davalar') {
                DcCategory::create([
                    'dc_cat_name' => $val,
                ]);
            }else {
                $mainCat = DcCategory::create([
                    'dc_cat_name' => $val,
                ]);

                $catThr = DcCategory::create([
                    'dc_cat_name' => 'Bireysel',
                ]);
                
                $catUnion = DcCategory::create([
                    'dc_cat_name' => 'Sendika',
                ]);

                $mainCat->childCategory()->save($catThr);
                $mainCat->childCategory()->save($catUnion);
            }
            
        }

        
    }
}

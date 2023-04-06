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
            'ADAY ÖĞRETMEN',
            'UZMAN ÖĞRETMEN',
            'BAŞ ÖĞRETMEN',
            'DAVALAR',
            'HUKUK VE DİĞER GÖRÜŞLER',
            'KAMU KURUMU DENETÇİLİĞİ (OMBUDSMANLIK)',
            'SORU ÖNERGESİ',
            'OLUR',
        ];

        foreach ($datas as $key => $val) {
            if($val !== 'DAVALAR') {
                DcCategory::create([
                    'dc_cat_name' => $val,
                ]);
            }else {
                $mainCat = DcCategory::create([
                    'dc_cat_name' => $val,
                ]);

                $catThr = DcCategory::create([
                    'dc_cat_name' => 'BİREYSEL',
                ]);
                
                $catUnion = DcCategory::create([
                    'dc_cat_name' => 'SENDİKA',
                ]);

                $mainCat->childCategory()->save($catThr);
                $mainCat->childCategory()->save($catUnion);
            }
            
        }

        
    }
}

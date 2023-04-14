<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\Settings;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Settings::create([
            'set_auth_signature_names' => 'mahmut özer|MAHMUT ÖZER|Mahmut Özer|PETEK AŞKAR|Petek Aşkar|petek aşkar|CEVDET VURAL|Cevdet Vural|cevdet vural|NECAT ALTIOK|Necat Altıok|necat altıok|AYŞE OĞUZ|Ayşe Oğuz|ayşe oğuz|UFUK DİLEKÇİ|Ufuk Dilekçi|ufuk dilekçi',
            'set_raw_auth_signature_names' => 'mahmut özer|petek aşkar|cevdet vural|necat altıok|ayşe oğuz|ufuk dilekçi',
            'set_allow_file_ext_names' => 'udf|UDF|udf|UDF|pdf|PDF|pdf|PDF|tif|TİF|tif|TIF|xlx|XLX|xlx|XLX|xlsx|XLSX|xlsx|XLSX|jpg|JPG|jpg|JPG|jpeg|JPEG|jpeg|JPEG|png|PNG|png|PNG|xml|XML|xml|XML|doc|DOC|doc|DOC|docx|DOCX|docx|DOCX',
            'set_raw_allow_file_ext_names' => 'udf|pdf|tif|xlx|xlsx|jpg|jpeg|png|xml|doc|docx',
        ]);
    }
}

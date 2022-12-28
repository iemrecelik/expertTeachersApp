<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\ModelsRepository\Admin\DcReportRepository;
use Illuminate\Database\Eloquent\Model;

class DcReport extends Model
{
    use HasFactory;

    use DcReportRepository;

    protected $fillable = [
        'rp_date',
        'rp_count',
        'user_id',
    ];
}

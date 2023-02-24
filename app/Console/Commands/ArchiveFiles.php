<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ArchiveFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature =  '
                                make:archive-files
                                {include : enter include files}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive files on server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $include = $this->argument('include');

        $datas = DB::table('dc_documents as t0')->select('
            t0.id, t0.dc_number, t0.dc_subject, 
            t0.dc_date,  t2.dc_cat_name,
            t3.dc_file_path
        ')
        ->join('dc_cat as t1', 't1.dc_id', '=', 't0.id')
        ->join('dc_category as t2', 't2.id', '=', 't0.id')
        ->join('dc_files as t3', 't3.dc_file_owner_id', '=', 't0.id')
        ->get();

        dd($datas);

        $this->error($include);

        return $include;
    }
}

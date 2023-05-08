<?php

namespace App\Console\Commands\Admin;

use Illuminate\Console\Command;

class RecordArchive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:archive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive documents';

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
        $archiveController = new \App\Http\Controllers\Admin\ArchiveController();
        $archiveController->zipArhive();
    }
}

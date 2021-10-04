<?php

namespace App\Console\Commands;

use App\Importers\Susanavet\Courses\ApiImporter;
use Illuminate\Console\Command;

class ImportSusanavetCourses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:susanavet-courses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Susanavet Courses';

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
     * @return mixed
     */
    public function handle()
    {
        app(ApiImporter::class)->run();
    }
}

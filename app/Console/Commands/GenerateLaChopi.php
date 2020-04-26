<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\LachopigenerationController;

set_time_limit(0);

class GenerateLaChopi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:lachopi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera la BD de la Chopi cada dia usando PHP 7.3';

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
        $result = new LachopigenerationController();
        $amount = $result->generate();

    }
}

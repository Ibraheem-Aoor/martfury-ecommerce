<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\v1\ProductController;
use Illuminate\Console\Command;

class FetchBolControlProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bol-control-products:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Products from bol control';

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
        (new ProductController())->getBolControlProductsAndStore();
    
    }
}

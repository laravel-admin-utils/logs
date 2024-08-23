<?php

namespace Elegant\Utils\OperationLog\Console;

use Elegant\Utils\OperationLog\OperationLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class InitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'operation-log:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize laravel-admin operation-log';

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
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function handle()
    {
        
        $this->initDatabase();
    }

    public function initDatabase()
    {
        $this->call('migrate');
        
        OperationLog::import();
    }
}

<?php

namespace Elegant\Utils\OperationLog\Console;

use Illuminate\Console\Command;

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
        $this->call('migrate');


        $menus_model = config('elegant-utils.admin.database.menus_model');

        // 如果不存在日志菜单，创建一个
        if (!$menus_model::query()->where('uri', 'operation_logs')->exists()) {
            // 创建菜单项
            $lastOrder = $menus_model::query()->max('order');
            $menus_model::query()->create([
                'parent_id' => 0,
                'order' => $lastOrder++,
                'title' => 'OperationLogs',
                'icon' => 'fas fa-history',
                'uri' => 'operation_logs',
            ]);

            $this->info('Initialization successful');
        }
    }
}

<?php

namespace Elegant\Utils\OperationLog\Console;

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
        $this->directory = config('elegant-utils.admin.directory');
        
        $this->createAuthLogController();

        $this->createAuthLogModel();

        $this->addRoutes();
        
        $this->initDatabase();
    }

    public function initDatabase()
    {
        $this->call('migrate');
        
        $menus_model = config('elegant-utils.admin.database.menu_model');

        // If the log menu does not exist, create one
        if (!$menus_model::query()->where('uri', 'auth/logs')->exists()) {
            // 创建菜单项
            $lastOrder = $menus_model::query()->max('order');
            $menus_model::query()->create([
                'parent_id' => 0,
                'order' => $lastOrder++,
                'title' => 'OperationLogs',
                'icon' => 'fas fa-history',
                'uri' => 'auth/logs',
            ]);

            $this->info('Initialization successful');
        }
    }


    /**
     * Create AuthLogController.
     *
     * @return void
     */
    public function createAuthLogController()
    {
        $controller = $this->directory.'\Controllers\AuthLogController.php';
        $contents = $this->getStub('AuthLogController');

        $this->laravel['files']->put(
            $controller,
            str_replace('DummyNamespace', config('elegant-utils.admin.route.namespace'), $contents)
        );
        $this->line('<info>AuthLogController file was created:</info> '.str_replace(base_path(), '', $controller));
    }

    /**
     * Create AuthLogModel.
     *
     * @return void
     */
    public function createAuthLogModel()
    {
        $model = app_path('Models\AuthLog.php');
        $contents = $this->getStub('AuthLog');

        $this->laravel['files']->put($model, $contents);
        $this->line('<info>AuthLog file was created:</info> '.str_replace(base_path(), '', $model));
    }

    /**
     * Add log routes
     *
     * @return void
     */
    public function addRoutes()
    {
        // If no log routing exists
        if (!Route::has('auth_logs.index')) {
            $routes = $this->directory . '\routes.php';
            $routes_contents = $this->laravel['files']->get($routes);

            $search = "        // done Don't delete this line of comment";
            $replace = $this->getStub('routes');

            $this->laravel['files']->put($routes, str_replace($search, $replace, $routes_contents));
        }
    }
    
    /**
     * Get stub contents.
     *
     * @param $name
     *
     * @return string
     */
    protected function getStub($name)
    {
        return $this->laravel['files']->get(__DIR__."/stubs/$name.stub");
    }
}

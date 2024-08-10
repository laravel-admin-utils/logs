<?php

namespace Elegant\Utils\OperationLog;

use Elegant\Utils\OperationLog\Http\Middleware\OperationLogMiddleware;
use Illuminate\Support\ServiceProvider;

class OperationLogServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        Console\InitCommand::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function boot(OperationLog $extension)
    {
        if (! OperationLog::boot()) {
            return ;
        }

        $this->app->booted(function () use ($extension) {
            OperationLog::routes($extension->routes);
        });

        if ($this->app->runningInConsole() && $database = $extension->database) {
            $this->publishes([$database => database_path()], 'admin-operation-log-database');
        }

        if ($this->app->runningInConsole() && $config = $extension->config) {
            $this->publishes([$config => config_path('elegant-utils')], 'admin-operation-log-config');
        }
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        app('router')->aliasMiddleware('admin.operation-log', OperationLogMiddleware::class);

        // 替换配置文件
        config(['elegant-utils.admin.route.middleware.log' => 'admin.operation-log',]);

        $this->commands($this->commands);
    }
}

# operation-log utils for laravel-admin

## preview

![operation_log_legend](resources/assets/legend.png)

## install

```shell
composer require laravel-admin-utils/operation-logs
```

## publish resources

```shell script
php artisan vendor:publish --provider="Elegant\Utils\OperationLog\OperationLogServiceProvider"
```

## initialize

```shell script
php artisan operation-log:init
```

open the link http://localhost/auth/logs

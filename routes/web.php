<?php

use Illuminate\Support\Facades\Route;

$AuthLogController = config('elegant-utils.operation_log.controller', Elegant\Utils\OperationLog\Http\Controllers\AuthLogController::class);

// auth_logs
Route::resource('auth/logs', $AuthLogController)->only(['index', 'destroy'])->names('auth_logs');
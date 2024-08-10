<?php

use Elegant\Utils\OperationLog\Http\Controllers\OperationLogController;
use Illuminate\Support\Facades\Route;

$logController = config('elegant-utils.operation_log.controller', OperationLogController::class);
Route::resource('operation_logs', $logController)->only(['index', 'destroy'])->names('operation_logs');

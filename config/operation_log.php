<?php

return [
    'enable' => true,

    'table' => 'operation_logs',

    'model' => Elegant\Utils\OperationLog\Models\OperationLog::class,

//    'controller' => Elegant\Utils\OperationLog\Http\Controllers\OperationLogController::class,

    'allowed_methods' => ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE', 'PATCH'],

    'hidden_keys' => ['_pjax', '_token', '_method', '_previous_', 'password', 'password_confirmation'],

    'except_paths' => [
        "operation_logs",
//        "auth_logs/*",
    ]
];

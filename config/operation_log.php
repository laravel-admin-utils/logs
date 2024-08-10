<?php

return [
    'enable' => true,

    'table' => 'operation_logs',

    'model' => Elegant\Utils\OperationLog\Models\OperationLog::class,

//    'controller' => Elegant\Utils\OperationLog\Http\Controllers\OperationLogController::class,

    'allowed_methods' => ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE', 'PATCH'],

    'excepts' => [
        "operation_logs",
//        "auth_logs/*",
    ]
];

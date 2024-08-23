<?php

return [
    'enable' => true,

    'table' => 'auth_logs',

    'model' => Elegant\Utils\OperationLog\Models\AuthLog::class,
    
    'controller' => Elegant\Utils\OperationLog\Http\Controllers\AuthLogController::class,

    'allowed_methods' => ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE', 'PATCH'],

    'hidden_keys' => ['_pjax', '_token', '_method', '_previous_', 'password', 'password_confirmation'],

    'excepts' => [
        "auth_logs.index",
        "auth_logs.destroy",
    ]
];

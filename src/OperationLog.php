<?php

namespace Elegant\Utils\OperationLog;

use Elegant\Utils\Extension;

class OperationLog extends Extension
{
    public $name = 'operation-logs';

    public $routes = __DIR__ . '/../routes/web.php';

    public $database = __DIR__ . '/../database';

    public $config = __DIR__ . '/../config';
}

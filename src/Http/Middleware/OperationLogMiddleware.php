<?php

namespace Elegant\Utils\OperationLog\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class OperationLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        if ($this->shouldLogOperation($request)) {
            try {
                $logModel = config('elegant-utils.operation_log.model');
                $logModel::create([
                    'user_id' => Auth::user()->id,
                    'operation' => str_replace('admin.', '', $request->route()->action['as']),
                    'path'    => $request->path(),
                    'method'  => $request->method(),
                    'ip'      => $request->getClientIp(),
                    'input'   => json_encode($request->input()),
                ]);
            } catch (\Exception $exception) {
                // pass
            }
        }

        return $next($request);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function shouldLogOperation(Request $request)
    {
        return Auth::user()
            && config('elegant-utils.operation_log.enable')
            && !$this->inExceptArray($request)
            && $this->inAllowedMethods($request->method());
    }

    /**
     * Whether requests using this method are allowed to be logged.
     *
     * @param string $method
     *
     * @return bool
     */
    protected function inAllowedMethods($method)
    {
        $allowedMethods = collect(config('elegant-utils.operation_log.allowed_methods'))->filter();

        if ($allowedMethods->isEmpty()) {
            return true;
        }

        return $allowedMethods->map(function ($method) {
            return strtoupper($method);
        })->contains($method);
    }

    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function inExceptArray($request)
    {
        $excludes = array_merge(config('elegant-utils.authorization.excepts'), config('elegant-utils.operation_log.excepts'));
        
        if (in_array(Route::current()->getName(), $excludes)) {
            return true;
        }

        return false;
    }
}

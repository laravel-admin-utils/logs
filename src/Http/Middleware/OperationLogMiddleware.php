<?php

namespace Elegant\Utils\OperationLog\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
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
            $request->setTrustedProxies(request()->getClientIps(), Request::HEADER_X_FORWARDED_FOR);

            $operation = array_map(function ($as) {
                return trans('admin.' . $as);
            }, explode('.', str_replace('admin.', '', $request->route()->action['as'])));

            $log = [
                'administrator_id' => Auth::user()->id,
                'operation' => implode('.', $operation),
                'path'    => $request->path(),
                'method'  => $request->method(),
                'ip'      => $request->getClientIp(),
                'input'   => json_encode($request->input()),
            ];

            try {
                $logModel = config('elegant-utils.operation_log.model');
                $logModel::create($log);
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
        return Auth::user() && config('elegant-utils.operation_log.enable') && !$this->inExceptArray($request) && $this->inAllowedMethods($request->method());
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
        foreach (config('elegant-utils.operation_log.excepts') as $except) {
            $except = admin_base_path($except);
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if (array_intersect(array_keys($request->input()), ['_pjax', '_token', '_method', '_previous_'])) {
                return true;
            }

            if ($request->is($except) || empty($request->route()->getAction('as')) || substr($request->route()->getAction('as'), 0,6) !== config('elegant-utils.admin.route.as')) {
                return true;
            }
        }

        return false;
    }
}

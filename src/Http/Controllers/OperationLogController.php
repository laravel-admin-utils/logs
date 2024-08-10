<?php

namespace Elegant\Utils\OperationLog\Http\Controllers;

use Elegant\Utils\Http\Controllers\AdminController;
use Elegant\Utils\Table;
use Illuminate\Support\Arr;

class OperationLogController extends AdminController
{
    /**
     * {@inheritdoc}
     */
    public function title()
    {
        return trans('admin.operation_logs');
    }

    /**
     * @return Table
     */
    protected function table()
    {
        $logModel = config('elegant-utils.operation_log.model');

        $table = new Table(new $logModel());
        $table->model()->orderByDesc('id');

        $table->column('id', 'ID')->sortable();
        $table->column('administrator.name', trans('admin.operator'));
        $table->column('operation', trans('admin.behave'))->display(function ($operation) {
            return trans($operation);
        });
        $table->column('method', trans('admin.http_method'))->display(function ($method) use ($logModel) {
            $color = Arr::get($logModel::$methodColors, $method, 'grey');
            return '<span class="badge bg-' . $color . '">' . $method . '</span>';
        });
        $table->column('path', trans('admin.http_uri'))->label('info');
        $table->column('ip', trans('admin.http_ip'))->label('info');
        $table->column('input', trans('admin.input'))->display(function () {
            return trans('admin.view');
        })->modal(trans('admin.view') . trans('admin.input'), function ($modal) {
            $input = json_decode($modal->input, true);
            $input = Arr::except($input, config('elegant-utils.operation_log.hidden_keys', []));

            if (empty($input)) {
                return '<pre>{}</pre>';
            }

            return '<pre>'. json_encode($input, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
        });

        $table->column('created_at', trans('admin.created_at'));

        $table->actions(function (Table\Displayers\Actions $actions) {
            $actions->disableEdit();
            $actions->disableView();
        });

        $table->disableCreateButton();

        $table->filter(function (Table\Filter $filter) use ($logModel) {
            $userModel = config('elegant-utils.admin.database.administrator_model');

            $filter->equal('administrator_id', trans('admin.administrator'))->select($userModel::pluck('name', 'id'));
            $filter->equal('method', trans('admin.http_method'))->select(array_combine($logModel::$methods, $logModel::$methods));
            $filter->like('path', trans('admin.http_uri'));
            $filter->equal('ip', trans('admin.http_ip'));
        });

        return $table;
    }

    /**
     * @param mixed $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $ids = explode(',', $id);

        $logModel = config('elegant-utils.operation_log.model');

        if ($logModel::destroy($ids)) {
            return $this->response(false)->success(trans('admin.delete_succeeded'))->refresh()->send();
        } else {
            return $this->response(false)->error(trans('admin.delete_failed'))->send();
        }
    }
}

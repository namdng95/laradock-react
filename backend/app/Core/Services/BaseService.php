<?php

namespace App\Core\Services;

use App\Core\Criteria\FilterCriteria;
use App\Core\Criteria\OrderCriteria;
use App\Core\Criteria\WithCountRelationsCriteria;
use App\Core\Criteria\WithRelationsCriteria;
use App\Helpers\DateTimeHelper;
use App\Exceptions\BusinessException;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Core\Contracts\RepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
abstract class BaseService
{
    const ACTION_LIST = 'list';
    const ACTION_COUNT = 'count';
    const ACTION_FIND = 'find';
    const ACTION_EXISTS = 'exists';

    protected $data;

    protected $model;

    protected $modelId;

    protected $repository;

    protected $handler;

    protected $selects = ['*'];
    protected $throwable = true;

    abstract public function handle();

    public function setOptions($request = null, $model = null, $handler = null)
    {
        $data = [];
        if (!is_null($request)) {
            if (is_array($request) || $request instanceof Collection) {
                $data = $request;
            }
            if (is_object($request) && method_exists($request, 'validated')) {
                $data = $request->validated();
            }
        }
        $this->setData($data);

        if (!is_null($model)) {
            $this->setModel($model);
        }

        $this->setHandler($handler);

        return $this;
    }

    public function setData($data = [])
    {
        $this->data = $data instanceof Collection ? $data : new Collection($data);
        $this->data = $this->data->filter(function ($value) {
            return !is_null($value);
        });

        if ($this->data->has('selects')) {
            $selects = $this->data->get('selects');
            if (is_string($selects)) {
                $explode = explode(',', $selects);
                $selects = [];
                foreach ($explode as $select) {
                    $selects[] = trim(strtolower($select));
                }
                if (!empty($selects)) {
                    $this->selects = $selects;
                }
            }
        }

        if ($this->data->has('throwable')) {
            $this->throwable = $this->data->get('throwable', true);
            $this->data->forget('throwable');
        }

        return $this;
    }

    private function initHandler($handler)
    {
        $this->handler = $handler;
        $this->data->forget('handler');
        return $this;
    }

    public function setHandler($handler = null)
    {
        if (!empty($handler) && $handler instanceof User) {
            return $this->initHandler($handler);
        }

        if (!$this->data->has('handler')
            && Auth::check()) {
            return $this->initHandler(authUser());
        }

        if ($this->data->has('handler')) {
            $handler = $this->data->get('handler');
            if ($handler instanceof User) {
                return $this->initHandler($handler);
            }
        }

        return $this;
    }

    public function setModel($model)
    {
        if ($model instanceof Model) {
            $this->model = $model;
            if (!empty($model->id)) {
                $this->modelId = $model->id;
            }
        }

        if (!$model instanceof Model) {
            $this->modelId = $model;
        }

        return $this;
    }

    public function notThrowable()
    {
        $this->throwable = false;
        return $this;
    }

    /* support data */

    public function inputId()
    {
        return $this->modelId ?? $this->data->get('id');
    }

    public function addWith($withs = '')
    {
        if (is_string($withs)) {
            $withs = explode(',', $withs);
        }
        $dataWiths = $this->data->get('with', '');
        if (is_string($dataWiths)) {
            $dataWiths = explode(',', $dataWiths);
        }
        if (!empty($withs) && !empty($dataWiths)) {
            $this->data->put('with', implode(',', array_merge($withs, $dataWiths)));
        }
    }

    public function addWithCount($withs = '')
    {
        if (is_string($withs)) {
            $withs = explode(',', $withs);
        }
        $dataWiths = $this->data->get('with_count', '');
        if (is_string($dataWiths)) {
            $dataWiths = explode(',', $dataWiths);
        }
        if (!empty($withs) && !empty($dataWiths)) {
            $this->data->put('with_count', implode(',', array_merge($withs, $dataWiths)));
        }
    }

    public function dataToArray($key)
    {
        $value = $this->data->get($key, '');
        if (is_string($value)) {
            $value = explode(',', $value);
        }
        foreach ($value as &$v) {
            $v = trim($v);
        }
        return $value;
    }

    public function findOrCheckExists(RepositoryInterface $repository = null)
    {
        if (is_null($repository)) {
            $repository = $this->repository;
        }
        if (is_null($repository)) {
            return null;
        }
        $action = $this->data->get('action', self::ACTION_FIND);
        if ($action == self::ACTION_EXISTS) {
            return $repository->exists();
        }
        $id = $this->inputId();

        if ($this->throwable) {
            return $id
                ? $this->repository->findOrFail($id, $this->selects)
                : $this->repository->firstOrFail($this->selects);
        }
        return $id
            ? $this->repository->find($id, $this->selects)
            : $this->repository->first($this->selects);
    }

    public function countOrPaginateOrAll(RepositoryInterface $repository = null)
    {
        if (is_null($repository)) {
            $repository = $this->repository;
        }
        if (is_null($repository)) {
            return null;
        }
        $action = $this->data->get('action', self::ACTION_LIST);
        if ($action == self::ACTION_COUNT) {
            return $repository->count();
        }
        $limit = 0;
        if ($this->data->has('per_page')) {
            $limit = intval($this->data->get('per_page'));
        }
        if ($this->data->has('limit')) {
            $limit = intval($this->data->get('limit'));
        }
        $limit = max($limit, 0);
        $limit = min($limit, 100);

        return $limit > 0
            ? $repository->paginate($limit, $this->selects)
            : $repository->get($this->selects);
    }

    public function dataDefaults($data = [])
    {
        foreach ($data as $key => $value) {
            $this->dataDefault($key, $value);
        }
    }

    public function dataDefault($key, $value)
    {
        if (!$this->data->has($key)) {
            $this->data->put($key, $value);
        }
    }

    public function dataToDatabaseDate($keys = [], $converts = '')
    {
        if (is_string($keys)) {
            $keys = explode(',', $keys);
        }
        foreach ($keys as $key) {
            $key = trim($key);
            if ($this->data->has($key)) {
                $value = $this->data->get($key);
                if (empty($value)) {
                    continue;
                }
                $date = DateTimeHelper::toDatabaseDate($value);
                $date = DateTimeHelper::convert($date, $converts);
                $this->data->put($key, $date);
            }
        }
    }

    public function dataToDatabaseTime($keys = [], $converts = '')
    {
        if (is_string($keys)) {
            $keys = explode(',', $keys);
        }
        foreach ($keys as $key) {
            $key = trim($key);
            if ($this->data->has($key)) {
                $value = $this->data->get($key);
                if (empty($value)) {
                    continue;
                }
                $time = DateTimeHelper::toDatabaseTime($value, true);
                $time = DateTimeHelper::convert($time, $converts);
                $this->data->put($key, $time);
            }
        }
    }

    public function dataToLower($keys = [])
    {
        if (is_string($keys)) {
            $keys = explode(',', $keys);
        }
        foreach ($keys as $key) {
            $key = trim($key);
            if ($this->data->has($key)) {
                $value = $this->data->get($key);
                $this->data->put($key, mb_strtolower($value));
            }
        }
    }

    public function dataToUpper($keys = [])
    {
        if (is_string($keys)) {
            $keys = explode(',', $keys);
        }
        foreach ($keys as $key) {
            $key = trim($key);
            if ($this->data->has($key)) {
                $value = $this->data->get($key);
                $this->data->put($key, mb_strtoupper($value));
            }
        }
    }

    /**
     * @throws ValidationException
     */
    public function exception($error, $key = '', $params = [])
    {
        if (!empty($key)) {
            throw ValidationException::withMessages([
                $key => [__('messages.'.$error, $params)]
            ]);
        }
        throw BusinessException::$error($params);
    }

    public function findCommon()
    {
        $this->repository->pushCriteria(new FilterCriteria($this->data->toArray()));
        $this->repository->pushCriteria(new WithRelationsCriteria($this->data->get('with')));
        $this->repository->pushCriteria(new WithCountRelationsCriteria($this->data->get('with_count')));

        return $this->findOrCheckExists();
    }

    public function listCommon()
    {
        $this->repository->pushCriteria(new FilterCriteria($this->data->toArray()));
        $this->repository->pushCriteria(new WithRelationsCriteria($this->data->get('with')));
        $this->repository->pushCriteria(new WithCountRelationsCriteria($this->data->get('with_count')));
        $this->repository->pushCriteria(new OrderCriteria($this->data->get('order', '-id')));

        return $this->countOrPaginateOrAll();
    }
}

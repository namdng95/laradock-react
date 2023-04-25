<?php

namespace App\Services;

use App\Core\Criteria\OrderCriteria;
use App\Core\Criteria\WithCountRelationsCriteria;
use App\Core\Criteria\WithRelationsCriteria;
use App\Enums\BooleanTypes;
use App\Exceptions\BusinessException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class Task
{
    public function __construct()
    {
        //
    }

    public function exception($error, $key = '', $params = [], $httpCode = Response::HTTP_BAD_REQUEST)
    {
        if (!empty($key)) {
            throw ValidationException::withMessages([
                $key => __('messages.' . $error)
            ]);
        }

        throw BusinessException::$error($params, $httpCode);
    }

    public function exceptions($errors = [])
    {
        $errorMessages = [];
        foreach ($errors as $key => $error) {
            $errorMessages[$key] = __('messages.' . $error);
        }
        if (!empty($errorMessages)) {
            throw ValidationException::withMessages($errorMessages);
        }
    }

    public function exceptionPermission()
    {
        $this->exception('permission_denied', '', [], Response::HTTP_FORBIDDEN);
    }

    public function isEncodeColumn($columnName)
    {
        $last5chars = substr($columnName, -5);
        return in_array($last5chars, ['uuid', 'code', '.uuid', '.code']);
    }

    public function querySearchLike($query, $value = '', $columns = [], $isRepository = true)
    {
        if (!hasSearch($value) || empty($columns)) {
            return $query;
        }
        if (is_string($columns)) {
            $columns = [$columns];
        }
        if (!is_array($columns)) {
            return $query;
        }
        if ($isRepository) {
            return $query->whereFunction(function ($subQuery) use ($columns, $value) {
                return $this->querySearchRaw($subQuery, $columns, $value);
            });
        }
        return $query->where(function ($subQuery) use ($columns, $value) {
            return $this->querySearchRaw($subQuery, $columns, $value);
        });
    }

    public function querySearchRaw($subQuery, $columns, $value)
    {
        $valueUuid = escapeStringUuid($value);
        $valueString = escapeString($value);
        foreach ($columns as $column) {
            $value = $this->isEncodeColumn($column)
                ? $valueUuid
                : $valueString;
            $subQuery = $subQuery->orWhereRaw('lower(' . $column . ') like (?)', "%{$value}%");
        }
        return $subQuery;
    }

    /**
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function querySearchWithCheckAll($data, $key, $query, $relation = '', $fieldIn = '')
    {
        $ids = convertToArray($data[$key . '_ids'] ?? '');
        $checkAll = ($data[$key . '_check_all'] ?? BooleanTypes::FALSE) == BooleanTypes::TRUE;
        if (empty($ids) && !$checkAll) {
            return $query;
        }
        if (empty($relation)) {
            if (!$checkAll) {
                $query = $query->whereIn($fieldIn, $ids);
                if (in_array(0, $ids)) {
                    $query = $query->orWhereNull($fieldIn);
                }
            }
            if ($checkAll) {
                $query = $query->whereNotIn($fieldIn, $ids);
            }
            return $query;
        }

        $query = $query->whereHas($relation, function ($sQ) use ($ids, $checkAll, $fieldIn) {
            if (!$checkAll) {
                $sQ = $sQ->whereIn($fieldIn, $ids);
            }
            if ($checkAll) {
                $sQ = $sQ->whereNotIn($fieldIn, $ids);
            }
            return $sQ;
        });
        if (($checkAll && !in_array(0, $ids))
            || (!$checkAll && in_array(0, $ids))) {
            $query = $query->orWhereDoesntHave($relation);
        }
        return $query;
    }

    public function loadCriterias($query, $data = [])
    {
        if (!empty($data['with'])) {
            $query = $query->pushCriteria(new WithRelationsCriteria($data['with']));
        }
        if (!empty($data['with_count'])) {
            $query = $query->pushCriteria(new WithCountRelationsCriteria($data['with_count']));
        }
        if (!empty($data['order'])) {
            $query = $this->pushOrder($query, $data['order']);
        }
        return $query;
    }

    public function getList($query, $data = [])
    {
        $data['order'] = $data['order'] ?? '-id';
        $query = $this->loadCriterias($query, $data);

        $limit = $data['limit'] ?? 0;
        $limit = max($limit, 0);
        $limit = min($limit, 200);

        if (!empty($data['take'])) {
            return $query->take($data['take'])->get();
        }

        return $limit > 0
            ? $query->paginate($limit)
            : $query->get();
    }

    public function pushOrder($query, $order)
    {
        $orders = convertToArray($order);
        $joined = [];
        foreach ($orders as $ind => $order) {
            $order = ltrim($order, '-');
            $explode = explode('.', $order);
            if (count($explode) > 1) {
                for ($i = 0; $i < count($explode) - 1; $i++) {
                    $table = $explode[$i];
                    if (!in_array($table, $joined)) {
                        $joined[] = $table;
                        $query = $query->joinTable($table);
                    }
                }
            }
            if (count($explode) > 2) {
                $orders[$ind] = $orders[$ind] == $order ? '' : '-';
                $orders[$ind] .= $explode[count($explode) - 2] . '.' . $explode[count($explode) - 1];
            }
        }
        return $query->pushCriteria(new OrderCriteria($orders));
    }

    public function getDetail($query, $data = [], $id = null)
    {
        $query = $this->loadCriterias($query, $data);

        if (!empty($data['no_throw'])) {
            return $id
                ? $query->find($id)
                : $query->first();
        }

        return $id
            ? $query->findOrFail($id)
            : $query->firstOrFail();
    }

    public function queryIn($query, $column, $values = '', $relation = '')
    {
        if ($values === '' || $values === []) {
            return $query;
        }
        if (!empty($relation)) {
            return $query->whereHas($relation, function ($q) use ($column, $values) {
                return $this->queryIn($q, $column, $values);
            });
        }
        if (!is_array($values)) {
            $values = convertToArray($values);
        }
        return $query->whereIn($column, $values);
    }
}

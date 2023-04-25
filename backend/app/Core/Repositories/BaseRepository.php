<?php

namespace App\Core\Repositories;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use App\Core\Contracts\RepositoryInterface;
use App\Core\Exceptions\RepositoryException;
use App\Core\Traits\HasCriteria;
use App\Core\Traits\HasScope;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class BaseRepository implements RepositoryInterface
{
    use HasScope, HasCriteria;

    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * @var \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected $model;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->resetModel();
        $this->resetScope();
        $this->resetCriteria();
    }

    /**
     * Get the model of repository
     *
     * @return string
     */
    abstract public function getModel();

    public static function getTable($isRaw = false): string
    {
        $tableName = (new static(app()))->model->getTable();
        if ($isRaw) {
            $tableName = config('database.prefix').$tableName;
        }
        return $tableName;
    }

    public function resetModel()
    {
        $instance = $this->app->make($this->getModel());

        if (! $instance instanceof Model) {
            throw RepositoryException::invalidModel();
        }

        return $this->model = $instance;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function joinTable($table = '')
    {
        return $this;
    }

    public function leftJoin($table, $callback)
    {
        $this->model = $this->model->leftJoin($table, $callback);

        return $this;
    }

    public function join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
    {
        if (empty($second) && !empty($operator)) {
            $second = $operator;
            $operator = '=';
        }

        $this->model = $this->model->join($table, $first, $operator, $second, $type, $where);

        return $this;
    }

    public function selectRaw($raw)
    {
        $this->model = $this->model->selectRaw($raw);

        return $this;
    }

    public function select($raw)
    {
        $this->model = $this->model->select($raw);

        return $this;
    }

    public function groupBy($field)
    {
        $this->model = $this->model->groupBy($field);

        return $this;
    }

    public function toSql()
    {
        return $this->model->toSql();
    }

    /**
     * Load relations
     *
     * @param array $relations
     * @return self
     */
    public function with($relations)
    {
        $this->model = $this->model->with($relations);

        return $this;
    }
    /**
     * Load relations
     *
     * @param array $relations
     * @return self
     */
    public function withTrashed()
    {
        $this->model = $this->model->withTrashed();

        return $this;
    }

    /**
     * Prepare for querying
     *
     * @return void
     */
    private function prepareQuery()
    {
        $this->applyCriteria();
        $this->applyScope();
    }

    /**
     * Rescue the query after performed
     *
     * @return void
     */
    private function rescueQuery()
    {
        $this->resetModel();
        $this->resetScope();
        $this->resetCriteria();
    }

    /**
     * Find record by id
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function find($id, array $columns = ['*'])
    {
        $this->prepareQuery();
        $result = $this->model->find($id, $columns);
        $this->rescueQuery();

        return $result;
    }

    public function chunkById(int $limit, Closure $callback)
    {
        $this->prepareQuery();
        $result = $this->model->chunkById($limit, $callback);
        $this->rescueQuery();

        return $result;
    }

    /**
     * firstOrCreate record
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function firstOrCreate(array $attributes = [], array $values = [])
    {
        if (empty($values)) {
            $values = $attributes;
        }
        $this->prepareQuery();
        $result = $this->model->firstOrCreate($attributes, $values);
        $this->rescueQuery();

        return $result;
    }

    /**
     * Find record by id
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findOrFail($id, array $columns = ['*'])
    {
        $this->prepareQuery();
        $result = $this->model->findOrFail($id, $columns);
        $this->rescueQuery();

        return $result;
    }

    public function whereFunction($function)
    {
        $this->model = $this->model->where($function);
        return $this;
    }

    public function whereHas($relation, $function = null)
    {
        $this->model = $this->model->whereHas($relation, $function);
        return $this;
    }

    public function whereRaw($query, $value)
    {
        $this->model = $this->model->whereRaw($query, $value);
        return $this;
    }

    public function where($column, $condition = '=', $value = null)
    {
        if (is_null($value)) {
            $value = $condition;
            $condition = '=';
        }

        $condition = strtolower($condition);
        switch ($condition) {
            case 'in':
                $this->model = $this->model->whereIn($column, $value);
                break;
            case 'not_in':
                $this->model = $this->model->whereNotIn($column, $value);
                break;
            case 'like':
            case 'ilike':
                $this->model = $this->model->where($column, $condition, '%'.$value.'%');
                break;
            case 'is_null':
                $this->model = $this->model->whereNull($column);
                break;
            case 'is_not_null':
                $this->model = $this->model->whereNotNull($column);
                break;
            default:
                $this->model = $this->model->where($column, $condition, $value);
                break;
        }
        return $this;
    }

    public function whereIn($column, $value)
    {
        $this->model = $this->model->whereIn($column, $value);

        return $this;
    }

    public function whereNotIn($column, $value)
    {
        $this->model = $this->model->whereNotIn($column, $value);

        return $this;
    }

    public function orWhere($column, $value)
    {
        $this->model = $this->model->orWhere($column, $value);

        return $this;
    }

    public function distinct()
    {
        $this->model = $this->model->distinct();

        return $this;
    }

    public function whereNotNull($field)
    {
        $this->model = $this->model->whereNotNull($field);

        return $this;
    }

    public function whereNull($field)
    {
        $this->model = $this->model->whereNull($field);

        return $this;
    }

    public function wheres($wheres = [])
    {
        foreach ($wheres as $where) {
            $column = $where[0];
            $condition = $where[1];
            if (!isset($where[2])) {
                $value = $condition;
                $condition = '=';
            }
            if (isset($where[2])) {
                $value = $where[2];
            }
            $this->where($column, $condition, $value);
        }

        return $this;
    }

    public function first(array $columns = ['*'])
    {
        $this->prepareQuery();
        $result = $this->model->first($columns);
        $this->rescueQuery();

        return $result;
    }

    public function firstOrFail(array $columns = ['*'])
    {
        $this->prepareQuery();
        $result = $this->model->firstOrFail($columns);
        $this->rescueQuery();

        return $result;
    }

    /**
     * Get all data of repository
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get(array $columns = ['*'])
    {
        $this->prepareQuery();
        $result = $this->model->get($columns);
        $this->rescueQuery();

        return $result;
    }

    /**
     * Get data of repository by pagination
     *
     * @param array $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $limit = null, array $columns = ['*'])
    {
        $this->prepareQuery();
        $result = $this->model->paginate($limit, $columns);
        $this->rescueQuery();

        return $result;
    }

    /**
     * Create new model
     *
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $attributes)
    {
        $result = $this->model->newInstance($attributes);
        $result->save();

        $this->resetModel();

        return $result;
    }

    /**
     * Update the existed model
     *
     * @param mixed $id
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($id, array $attributes)
    {
        $this->applyScope();

        $result = $this->model->where('id', $id)->update($attributes);

        $this->resetScope();
        $this->resetModel();

        return $result;
    }

    public function updateAll(array $attributes)
    {
        $this->applyScope();

        $result = $this->model->update($attributes);

        $this->resetScope();
        $this->resetModel();

        return $result;
    }

    /**
     * Remove the existed model
     *
     * @param mixed $id
     * @return boolean
     */
    public function delete($id = null)
    {
        $this->prepareQuery();
        if ($id) {
            $this->model = $this->model->where('id', $id);
        }
        $result = $this->model->delete();
        $this->rescueQuery();

        return $result;
    }

    /**
     * Remove the existed model
     *
     * @param mixed $id
     * @return boolean
     */
    public function deleteAll()
    {
        $this->prepareQuery();
        $result = $this->model->delete();
        $this->rescueQuery();

        return $result;
    }

    /**
     * Update or Create an entity in repository
     *
     * @param array $attributes
     * @param array $values
     *
     * @return mixed
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        $this->prepareQuery();
        $result = $this->model->updateOrCreate($attributes, $values);
        $this->rescueQuery();

        return $result;
    }

    /**
     * Count records
     * @return int
     */
    public function count()
    {
        $this->prepareQuery();
        $result = $this->model->count();
        $this->rescueQuery();

        return $result;
    }

    /**
     * Pluck column
     *
     * @param string $column
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck($column)
    {
        $this->prepareQuery();
        $result = $this->model->pluck($column);
        $this->rescueQuery();

        return $result;
    }

    /**
     * Insert new records
     *
     * @param array $values
     * @return boolean
     */
    public function insert(array $values)
    {
        return $this->model->insert($values);
    }

    /**
     * Chunk records
     *
     * @return bool
     */
    public function chunk(int $quantity, Closure $callback)
    {
        $this->prepareQuery();

        return $this->model->chunk($quantity, $callback);
    }

    /**
     * Check that records is exists
     *
     * @return bool
     */
    public function exists()
    {
        $this->prepareQuery();
        $result = $this->model->exists();
        $this->rescueQuery();

        return $result;
    }

    /**
     * Retrieve the sum of the values of a given column.
     *
     * @param string $column
     */
    public function sum(string $column)
    {
        $this->prepareQuery();
        $result = $this->model->sum($column);
        $this->rescueQuery();

        return $result;
    }

    /**
     * Limit data
     *
     * @param int $number
     */
    public function take(int $number)
    {
        $this->prepareQuery();
        $result = $this->model->take($number);
        $this->rescueQuery();

        return $result;
    }

    /**
     * Add "order by" clause to the query.
     *
     * @param  string  $column
     * @return $this
     */
    public function orderBy($column)
    {
        $this->prepareQuery();
        $result = $this->model->orderBy($column);
        $this->rescueQuery();

        return $result;
    }

    /**
     * Add a relationship count / exists condition to the query with where clauses.
     *
     * @param string $relation
     * @param $function
     * @return $this
     */
    public function whereDoesntHave(string $relation, $function)
    {
        $this->model = $this->model->whereDoesntHave($relation, $function);
        return $this;
    }
}

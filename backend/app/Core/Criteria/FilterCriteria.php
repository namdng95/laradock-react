<?php

namespace App\Core\Criteria;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\Core\Contracts\CriteriaInterface;
use App\Core\Contracts\RepositoryInterface;

/**
 * Class FilterCriteria.
 *
 * @package App\Core\Criteria;
 */
class FilterCriteria implements CriteriaInterface
{
    /**
     * @var array|\Illuminate\Support\Collection
     */
    protected $input;

    /**
     * List of allowable fiters
     *
     * @var array|null
     */
    protected $allows;

    /**
     * @var array
     */
    protected $relationFilters = [];

    /**
     * Instance of FilterCriteria
     *
     * @param array|\Illuminate\Support\Collection $input
     * @param array $allows
     */
    public function __construct($input, array $allows = null)
    {
        $this->input = [];
        if ($input instanceof Collection) {
            $this->input = $input->all();
        } elseif (is_array($input)) {
            $this->input = $input;
        }

        $this->allows = $allows;
    }

    /**
     * Apply criteria in query repository
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        if (is_null($this->allows)
            && method_exists($repository, 'filterableFields')) {
            $this->allows = $repository->filterableFields();
        }
        if (is_null($this->allows)) {
            return $model;
        }

        foreach ($this->allows as $key => $value) {
            $filterName = is_string($key) ? $key : $value;
            $filter = is_string($key) ? $value : $this->getFilter($value);

            if (! isset($filterName) || ! isset($this->input[$filterName])) {
                continue;
            }

            if ($this->isValidFilter($filter)) {
                $model = $filter::apply($model, $this->input[$filterName], $repository);
                $this->prepareRelationFilters($filter, $this->input[$filterName]);

                continue;
            }

            $model = $model->where($filterName, $this->input[$filterName]);
        }

        return $this->applyRelationFilterQuery($model);
    }

    private function getFilter($filterName)
    {
        return 'App\\Filters\\' . Str::studly($filterName);
    }

    private function isValidFilter($filter)
    {
        return class_exists($filter);
    }

    private function prepareRelationFilters($classFilterName, $input)
    {
        $filters = preg_grep('/^(has|doesntHave)/', get_class_methods($classFilterName));

        foreach ($filters as $filter) {
            if (! array_key_exists($filter, $this->relationFilters)) {
                $this->relationFilters[$filter] = [];
            }

            $this->relationFilters[$filter][$classFilterName . '::' . $filter] = $input;
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $model
     */
    private function applyRelationFilterQuery($model)
    {
        foreach ($this->relationFilters as $key => $filters) {
            $clause = Str::startsWith($key, 'has') ? 'whereHas' : 'whereDoesntHave';
            $relation = Str::startsWith($key, 'has') ? Str::substr($key, 3) : Str::substr($key, 10);

            $model = $model->$clause(Str::camel($relation), function ($query) use ($filters) {
                foreach ($filters as $filter => $input) {
                    $query = $filter($query, $input);
                }
            });
        }

        return $model;
    }
}

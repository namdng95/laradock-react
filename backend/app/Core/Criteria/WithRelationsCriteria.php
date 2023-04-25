<?php

namespace App\Core\Criteria;

use App\Core\Contracts\CriteriaInterface;
use App\Core\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class WithRelationsCriteriaCriteria.
 *
 * @package App\Core\Criteria;
 */
class WithRelationsCriteria implements CriteriaInterface
{
    /**
     * List of request relations from query string
     *
     * @var array
     */
    protected $input;

    /**
     * List of allow relations
     *
     * @var array|null
     */
    protected $allows;

    /**
     * An constructor of WithRelationsCriteria
     *
     * @param mixed $input
     * @param array|null $allows
     */
    public function __construct($input = '', $allows = [])
    {
        $this->input = array_filter(
            array_map(
                '\Illuminate\Support\Str::camel',
                is_array($input) ? $input : explode(',', $input)
            )
        );
        foreach ($this->input as &$input) {
            $input = trim($input);
        }

        $this->allows = $allows;
    }

    /**
     * Apply criteria in query repository
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param \App\Core\Contracts\RepositoryInterface $repository
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $tmpModel = $model;
        if ($model instanceof Builder) {
            $tmpModel = $model->getModel();
        }

        $withs = [];
        foreach ($this->input as $with) {
            if (in_array($with, $withs)) {
                continue;
            }
            if (strpos($with, '.') !== false) {
                $parser = explode('.', $with);
                if (method_exists($tmpModel, $parser[0])) {
                    $withs[] = $with;
                }
            }
            if (method_exists($tmpModel, $with)) {
                $withs[] = $with;
            }
        }

        return empty($withs) ? $model : $model->with($withs);
    }
}

<?php

namespace App\Core\Criteria;

use App\Core\Contracts\CriteriaInterface;
use App\Core\Contracts\RepositoryInterface;

/**
 * Class NewOrderCriteriaCriteria.
 *
 * @package App\Core\Criteria;
 */
class OrderCriteria implements CriteriaInterface
{
    /**
     * @var array $order
     */
    protected $orders;

    /**
     * Instance of Order2Criteria
     *
     * @param array|string $input
     */
    public function __construct($input)
    {
        $this->orders = array_filter(is_array($input) ? $input : explode(',', $input));
        foreach ($this->orders as &$order) {
            $order = trim($order);
        }
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
        foreach ($this->orders as $order) {
            $desc = substr($order, 0, 1) === '-';
            $field = $desc ? substr($order, 1) : $order;

            $model = $desc ? $model->orderByDesc($field) : $model->orderBy($field);
        }
        return $model;
    }
}

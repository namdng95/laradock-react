<?php

namespace App\Core\Contracts;

use App\Core\Contracts\RepositoryInterface;

interface CriteriaInterface
{
    /**
     * Apply the criteria
     *
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $model
     * @param RepositoryInterface $repository
     * @return void
     */
    public function apply($model, RepositoryInterface $repository);
}

<?php

namespace App\Core\Events;

use Illuminate\Database\Eloquent\Model;

class RepositoryEntityCreated
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}

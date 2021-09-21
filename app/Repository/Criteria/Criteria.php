<?php

namespace App\Repository\Criteria;

use App\Repository\Contracts\RepositoryInterface as Repository;

abstract class Criteria
{

    /**
     * @param $model
     * @param Repository $repository
     * @return mixed
     */
    public abstract function apply($model, Repository $repository);
}

<?php

namespace App\Repository\Eloquent;

use App\Models\Manager;
use App\Repository\Eloquent\Repository;

class AdminRepository extends Repository
{
    protected function model()
    {
        return Manager::class;
    }
}

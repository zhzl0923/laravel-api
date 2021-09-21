<?php

namespace App\Services;

use App\Repository\Eloquents\AdminRepository;

class AdminService
{
    /**
     * @var AdminRepository
     */
    protected $repostory;

    public function __construct(AdminRepository $repostory)
    {
        $this->repostory = $repostory;
    }

    public function handleSearchList()
    {
        return $this->repostory->paginate();
    }
}

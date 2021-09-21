<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Response\Facade\Response;
use App\Services\AdminService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $service;

    public function __construct(AdminService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return Response::success($this->service->handleSearchList());
    }
}

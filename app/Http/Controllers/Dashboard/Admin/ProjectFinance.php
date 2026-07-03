<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Dashboard\BaseController;
use Illuminate\Http\Request;

class ProjectFinance extends BaseController
{
    public function index(){
     $this->shareCommonData();
     return view('dashboard.index', [
         'View' => 'dashboard.admin.projectFinance.index',
         'title' => 'Бюджет и финансирование проекта | Администрирование',
         'PagName' => 'Бюджет и финансирование проекта',
         'InPageName' => 'Управление группами товаров',
         'CreateObject' => '/admin/productGroups/create'
     ]);
    }
}

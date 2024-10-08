<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $records = Role::getAll();

        return view('roles.index', compact('records'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Customer;

class DashboardController extends Controller
{
    public function dashboard(){

        $users = Customer::get();

        return Inertia::render('Dashboard', [
            'users' => $users
        ]);
    }
}

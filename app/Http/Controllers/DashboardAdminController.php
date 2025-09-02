<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;

class DashboardAdminController extends Controller
{


    public function index()
    {
        $office = Office::get(); // Ambil kantor user
        return view('user.dashboard', compact('office'));
    }
}
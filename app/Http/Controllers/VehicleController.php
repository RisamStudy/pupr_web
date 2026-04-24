<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    function index()
    {
        $vehicles = Vehicle::all();

        return response()->json($vehicles);
    }
}

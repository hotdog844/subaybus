<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoutePlannerController extends Controller
{
    public function index()
    {
        // For the prototype, we just need to show the view.
        // The logic will be in the JavaScript on the page itself.
        return view('route_planner');
    }
}
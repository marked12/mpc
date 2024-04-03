<?php

namespace App\Http\Controllers;

class CancelController extends Controller
{
    public function index()
    {
        // Redirect or perform any other action upon cancel
        return redirect()->route('general.client_dashboard'); // Replace 'home' with your desired route
    }
}

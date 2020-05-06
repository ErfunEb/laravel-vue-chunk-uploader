<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{

    // renders the vue SPA
    public function index() {
        return view('upload');
    }

}

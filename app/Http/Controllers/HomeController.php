<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $module;
    private $baseview;
    public function __construct()
    {
        $this->module='home';
        $this->baseview='home';
        $this->middleware('auth');
        $this->middleware('hooks');
    }

    public function index()
    {
        checkAccess();
        return view($this->module."/".$this->baseview);
    }
}

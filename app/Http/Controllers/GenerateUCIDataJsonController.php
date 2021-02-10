<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class GenerateUCIDataJsonController extends Controller
{
    public function __invoke()
    {
        Artisan::call('generate:json-uci-data');
    }
}

<?php

namespace App\Http\Controllers\Color;

use App\Http\Controllers\Controller;
use App\Http\Requests\ColorRequest;
use App\Models\Color;
class IndexController extends Controller
{
    public function index()
    {
        $product = Color::all();
        return json_decode(json_encode($product), true);
    }
}

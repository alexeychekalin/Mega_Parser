<?php

namespace App\Http\Controllers\Type;

use App\Http\Controllers\Controller;
use App\Models\Type;
class IndexController extends Controller
{
    public function index()
    {
        $product = Type::all();
        return json_decode(json_encode($product), true);
    }
}

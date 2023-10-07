<?php

namespace App\Http\Controllers\Type;

use App\Http\Controllers\Controller;
use App\Models\Type;
class IndexController extends Controller
{
    public function index()
    {
        $product = Type::orderBy('typeName')->get();
        return json_decode(json_encode($product), true);
    }
}

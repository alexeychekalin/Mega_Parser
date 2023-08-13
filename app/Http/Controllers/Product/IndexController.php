<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductRequest;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class IndexController extends Controller
{
    public function index()
    {
        $product = Product::all();
        return json_decode(json_encode($product), true);
    }
}

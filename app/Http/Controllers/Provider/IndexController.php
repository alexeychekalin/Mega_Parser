<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProviderRequest;
use App\Models\Provider;
class IndexController extends Controller
{
    public function index()
    {
        $product = Provider::all();
        return json_decode(json_encode($product), true);
    }
}

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
        $product = DB::table('product')
                    ->select('product.*', 'types.typeName',
                        DB::raw('(CASE WHEN product.Bonus = 0 THEN "Нет" ELSE "Да" END) as Bonus'),
                        DB::raw('(CASE WHEN product.CardCash = 0 THEN "Нет" ELSE "Да" END) as CardCash'),
                        DB::raw('(CASE WHEN product.Monitor = 0 THEN "Нет" ELSE "Да" END) as Monitor')
                    )
                    ->join('types', 'product.ProductId', '=', 'types.typeID')
                    ->get();
        return json_decode(json_encode($product), true);
    }
}

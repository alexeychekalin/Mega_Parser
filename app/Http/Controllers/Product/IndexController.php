<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductRequest;
use App\Models\Product;
use Hamcrest\Core\IsNull;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;


class IndexController extends Controller
{
    public function index()
    {
        $product['values'] = DB::table('product')
                    ->select('product.*', 'types.typeName', 'providers.providerName', 'types.commission', 'providers.providerID',
                        DB::raw('(CASE WHEN product.Bonus = 0 THEN false WHEN product.Bonus is NULL THEN false ELSE true END) as Bonus'),
                        DB::raw('(CASE WHEN product.CardCash = 0 THEN false WHEN product.CardCash is NULL THEN false ELSE true END) as CardCash'),
                        DB::raw('(CASE WHEN product.Monitor = 0 THEN false WHEN product.Monitor is NULL THEN false ELSE true END) as Monitor')
                    )
                    ->join('types', 'product.Type', '=', 'types.typeID')
                    ->leftJoin('providers', 'product.Wholesaler', '=', 'providers.providerID')
                    ->whereNotIn('types.typeId', [99,100,101])
                    // ->whereBetween('parseDate', [Carbon::now()->subDay(30), Carbon::now()])
                    ->where('product.Monitor', '=', 1)
                    ->whereNotNull('product.Wholesaler')
                    ->get();
        $product['tax'] = (new \App\Http\Controllers\TaxController)->tax();
        $product['rent'] = (new \App\Http\Controllers\TaxController)->rent();;
        return json_decode(json_encode($product), true);
    }
}

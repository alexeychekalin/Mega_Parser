<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductRequest;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;


class IndexController extends Controller
{
    public function index()
    {
        $product = DB::table('product')
                    ->select('product.*', 'types.typeName', 'providers.providerName',
                        DB::raw('(CASE WHEN product.Bonus = 0 THEN "Нет" WHEN product.Bonus is NULL THEN "Нет" ELSE "Да" END) as BonusVal'),
                        DB::raw('(CASE WHEN product.CardCash = 0 THEN "Нет" WHEN product.CardCash is NULL THEN "Нет" ELSE "Да" END) as CardCashVal'),
                        DB::raw('(CASE WHEN product.Monitor = 0 THEN "Нет" WHEN product.Monitor is NULL THEN "Нет" ELSE "Да" END) as MonitorVal')
                    )
                    ->join('types', 'product.Type', '=', 'types.typeID')
                    ->leftJoin('providers', 'product.Wholesaler', '=', 'providers.providerID')
                    ->whereNotIn('types.typeId', [99,100,101])
                    ->whereBetween('parseDate', [Carbon::now()->subDay(15), Carbon::now()])
                    ->get();
        return json_decode(json_encode($product), true);
    }
}
<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class ProductController extends Controller
{
    public function index(ProductRequest $request)
    {
        $data = $request->validated();
        return Product::create($data);
    }

    public function update(ProductRequest $request){
        DB::table('product')
            ->where('ProductId', $request['ProductId'])
            ->update(
                [
                    'Type' => $request['Type'],
                    'ClassName' => $request['ClassName'],
                    'PurchasePrice' => $request['PurchasePrice'],
                    'Bonus' => DB::raw($request['Bonus']),
                    'CardCash' => DB::raw($request['CardCash']),
                    'Monitor' => DB::raw($request['Monitor']),
                    'SellPrice' => $request['SellPrice'] == "" ? null : $request['SellPrice'],
                ]
            );
        return $request['id'];
    }
    public function delete(Request $request){
        Product::where('ProductID', $request['ProductID'])->delete();
        return $request['ProductID'];
    }

    public function get99()
    {
        $product = DB::table('product')
            ->select('product.*', 'types.typeName',
                DB::raw('(CASE WHEN product.Bonus = 0 THEN "Нет" WHEN product.Bonus is NULL THEN "Нет" ELSE "Да" END) as BonusVal'),
                DB::raw('(CASE WHEN product.CardCash = 0 THEN "Нет" WHEN product.CardCash is NULL THEN "Нет" ELSE "Да" END) as CardCashVal'),
                DB::raw('(CASE WHEN product.Monitor = 0 THEN "Нет" WHEN product.Monitor is NULL THEN "Нет" ELSE "Да" END) as MonitorVal')
            )
            ->join('types', 'product.Type', '=', 'types.typeID')
            ->whereNotIn('types.typeId', [99,100,101])
            ->get();
        return json_decode(json_encode($product), true);
    }
}

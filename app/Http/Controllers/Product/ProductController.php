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
            ->where('ProductID', $request['ProductID'])
            ->update(
                [
                    'Type' => $request['Type'],
                    'ClassName' => DB::raw($request['ClassName']),
                    'PurchasePrice' => $request['PurchasePrice'],
                    'Bonus' => DB::raw($request['Bonus']),
                    'CardCash' => DB::raw($request['CardCash']),
                    'Monitor' => DB::raw($request['Monitor']),
                    'SellPrice' => DB::raw($request['SellPrice'])
                ]
            );
        return $request['id'];
    }
    public function delete(Request $request){
        Product::where('ProductID', $request['ProductID'])->delete();
        return $request['ProductID'];
    }

    public function getType()
    {
       return DB::table('types')->get();
    }

}

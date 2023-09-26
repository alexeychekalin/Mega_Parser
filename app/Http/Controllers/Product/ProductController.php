<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductRequest;
use App\Models\Product;
use Carbon\Carbon;
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
                    'Model' => $request['Model'],
                    'PurchasePrice' => $request['PurchasePrice'],
                    'Bonus' => $request['Bonus'],
                    'CardCash' => $request['CardCash'],
                    'Monitor' => $request['Monitor'],
                    'SellPrice' => $request['SellPrice'] == "" ? null : $request['SellPrice'],
                    'Color' => $request['Color'],
                    'Rostest' =>  $request['Rostest'] == "" ? 0 : $request['Rostest'],
                    'Wholesaler' => $request['Wholesaler'] == "" ? null : $request['Wholesaler'],
                    'Retailer' => $request['Retailer'] == "" ? null : $request['Retailer'],
                ]
            );
        return $request['id'];
    }
    public function delete(Request $request){
        Product::where('ProductID', $request['ProductID'])->delete();
        return $request['ProductID'];
    }

    public function geterrors()
    {
        $product = DB::table('product')
            ->select('product.*', 'types.typeName', 'providers.providerName')
            ->leftJoin('types', 'product.Type', '=', 'types.typeID')
            ->leftJoin('providers', 'product.Wholesaler', '=', 'providers.providerID')
            ->whereIn('types.typeId', [100,101])
            ->whereNotNull('product.Wholesaler')
            ->get();
        return json_decode(json_encode($product), true);
    }

    public function getnotype()
    {
        $product = DB::table('product')
            ->select('product.*', 'types.typeName', 'providers.providerName')
            ->leftJoin('types', 'product.Type', '=', 'types.typeID')
            ->leftJoin('providers', 'product.Wholesaler', '=', 'providers.providerID')
            ->where('types.typeId', '=', 99)
            ->whereNotNull('product.Wholesaler')
            ->get();
        return json_decode(json_encode($product), true);
    }

    public function getsmm()
    {
        $product = DB::table('product')
            ->select('product.*', 'types.typeName', 'providers.providerName')
            ->leftJoin('types', 'product.Type', '=', 'types.typeID')
            ->leftJoin('providers', 'product.Wholesaler', '=', 'providers.providerID')
            ->where('types.typeId', '=', 102)
            ->whereNotNull('product.Wholesaler')
            ->get();
        return json_decode(json_encode($product), true);
    }

    public function monitor(){
        $product = DB::table('product')
            ->select('product.*', 'types.typeName', 'providers.providerName',
                DB::raw('(CASE WHEN product.Bonus = 0 THEN false WHEN product.Bonus is NULL THEN false ELSE true END) as Bonus'),
                DB::raw('(CASE WHEN product.CardCash = 0 THEN false WHEN product.CardCash is NULL THEN false ELSE true END) as CardCash'),
                DB::raw('(CASE WHEN product.Monitor = 0 THEN false WHEN product.Monitor is NULL THEN false ELSE true END) as Monitor')
            )
            ->leftJoin('types', 'product.Type', '=', 'types.typeID')
            ->leftJoin('providers', 'product.Wholesaler', '=', 'providers.providerID')
            ->whereNotIn('types.typeId', [99, 100, 101, 102])
           // ->where('product.Monitor', '=', 0)->orWhereNull('product.Monitor')
            ->whereNotNull('product.Wholesaler')
            ->get();
        return json_decode(json_encode($product), true);
    }
    public function set(Request $request): void
    {
        DB::table('product')
            ->where('ProductId', $request['ProductId'])
            ->update(
                [
                    $request['what'] => $request['set'],
                ]
            );
    }
    public function setbytype(Request $request){
        DB::table('product')
            ->where('Type', $request['typeID'])
            ->update(
                [
                    'Monitor' => $request['set'],
                ]
            );
        return $request['typeID'];
    }

    public function settype(Request $request): void
    {
        DB::table('product')
            ->where('ProductId', $request['ProductId'])
            ->update(
                [
                    'Type' => $request['Type'],
                ]
            );
    }

    public function stats()
    {
        $product = DB::table('product' )
            ->select('product.Type', 'types.typeName', DB::raw('COUNT(Type) as count'))
            ->leftJoin('types', 'product.Type', '=', 'types.typeID')
            ->whereIn('types.typeId', [99, 100, 101, 102])
            ->groupBy('types.typeName', 'product.Type')
            ->whereNotNull('product.Wholesaler')
            ->get();
        return json_decode(json_encode($product), true);
    }

    public function countMonitor()
    {
        $cnt = DB::table('product' )
            ->select(DB::raw('COUNT(ProductId) as count'))
            ->where('Monitor', '=', 1)
            ->get();
        return json_decode(json_encode($cnt), true);
    }

}

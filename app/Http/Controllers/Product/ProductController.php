<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                    'LinkToSMM' => ''
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
            ->where('product.SMMNotFound', '=', 1)
            //->whereNotNull('product.Wholesaler')
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
            ->whereNotIn('types.typeId', [99, 100, 101])
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

    public function getSimilarType(Request $request)
    {
        $product = DB::select('SELECT Model, ProductId FROM product WHERE DAMLEVP(Model, :model) < 0.5 and Type = 99', ['model' => $request['Model']]);
        return json_decode(json_encode($product), true);
    }

    public function getSimilarTypeTrash(Request $request)
    {
        $product = DB::select('SELECT Model, ProductId FROM product WHERE DAMLEVP(Model, :model) < 0.5 and Type in (100, 101)', ['model' => $request['Model']]);
        return json_decode(json_encode($product), true);
    }

    public function updateSimilarType(Request $request)
    {
        DB::table('product' )
            ->whereIn('ProductId', $request['Models'])
            ->update(['Type' => $request['Type']]);
        return $request['Models'];
    }

    public function trashSimilar(Request $request): void
    {
       foreach ($request['Models'] as $i){
           DB::table('trash')->upsert(
               ['trashString' => $i['Model']],
               ['trashString'],
               ['trashString']
           );
           Product::where('ProductId', $i['ProductId'])->delete();
       }

    }

    public function countMonitor()
    {
        $cnt = DB::table('product' )
            ->select(DB::raw('COUNT(ProductId) as count'))
            ->where('Monitor', '=', 1)
            ->whereNotNull('product.Wholesaler')
            ->get();
        return json_decode(json_encode($cnt), true);
    }

    public function countProducts()
    {
        $cnt = DB::table('product' )
            ->select(DB::raw('COUNT(ProductId) as count'))
            ->get();
        return json_decode(json_encode($cnt), true);
    }

    public function stats()
    {
        $product = DB::table('product' )
            ->select('product.Type', 'types.typeName', DB::raw('COUNT(Type) as count'))
            ->leftJoin('types', 'product.Type', '=', 'types.typeID')
            ->whereIn('types.typeId', [99, 100, 101])
            ->groupBy('types.typeName', 'product.Type')
            ->whereNotNull('product.Wholesaler')
            ->get();
        $nosmm = DB::table('product' )
            ->select( DB::raw('COUNT(SMMNotFound) as count'))
            ->where('SMMNotFound', '=', 1)
            ->get();
        $product[] = ['Type' => 102, 'typeName' => 'Не найдено на СММ', 'count' => $nosmm[0]->count];

        $answer[0] = $product;
        $answer[1] = [
            'monitor' => $this->countMonitor()[0]['count'],
            'products' => $this->countProducts()[0]['count'],
            'type' => count((new \App\Http\Controllers\Type\TypeController)->monitor()),
            'typeAll' => count((new \App\Http\Controllers\Type\TypeController)->getall()),
            'colors' => count((new \App\Http\Controllers\Color\IndexController())->index()),
            'providers' => count((new \App\Http\Controllers\Provider\IndexController())->index()),
            'users' => count((new \App\Http\Controllers\User\IndexController())->index()),

        ];


        return json_decode(json_encode($answer), true);
    }

    public function addEdits(Request $request)
    {
        DB::table('edits')->upsert(
            [
            'OldModel' => $request['old'],
            'NewModel' => $request['new']
            ],
            ['OldModel'],
            ['NewModel']
        );
    }

    public function addTrash(Request $request)
    {
        DB::table('trash')->upsert(
            [
                'trashString' => $request['trashString'],
            ],
            ['trashString'],
            ['trashString']
        );

        $this->delete($request);
    }

}

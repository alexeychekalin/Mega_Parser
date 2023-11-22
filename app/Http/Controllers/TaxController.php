<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class TaxController extends Controller
{
    public function index()
    {
        $tax = Tax::all();
        return json_decode(json_encode($tax), true);
    }

    public function update(Request $request){
        $cnt = count($request['tax']);
        $j = 0;
        while($j < $cnt){
            DB::table('taxes')
                ->where('id', $request['tax'][$j]['id'])
                ->update(
                    [
                        'value' => $request['tax'][$j]['value'],
                    ]
                );
            $j++;
        }
    }

    public function tax(){
        $tax = DB::table('taxes')
            ->select('value')
            ->where('id', 1)
            ->get();

        return json_decode(json_encode($tax), true);
    }

    public function rent(){
       $rent = DB::table('taxes')
            ->select('value')
            ->where('id', 2)
            ->get();

        return json_decode(json_encode($rent), true);
    }
}

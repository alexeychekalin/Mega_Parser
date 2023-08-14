<?php

namespace App\Http\Controllers\Type;

use App\Http\Controllers\Controller;
use App\Http\Requests\TypeRequest;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class TypeController extends Controller
{
    public function index(TypeRequest $request)
    {
        $data = $request->validated();
        return Type::create($data);
    }

    public function update(TypeRequest $request){
        DB::table('types')
            ->where('typeID', $request['typeID'])
            ->update(
                [
                    'typeName' => $request['typeName']
                ]
            );
        return $request['id'];
    }
    public function delete(Request $request){
        Type::where('typeID',$request['typeID'])->delete();
    }
}

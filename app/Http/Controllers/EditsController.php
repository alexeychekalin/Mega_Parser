<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditsRequest;
use App\Models\Edits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class EditsController extends Controller
{
    public function index()
    {
        $product = Edits::all();
        return json_decode(json_encode($product), true);
    }

    public function insert(Request $request): void
    {
        DB::table('edits')->upsert(
            [
                'OldModel' => $request['OldModel'],
                'NewModel' => $request['NewModel']
            ],
            ['OldModel'],
            ['NewModel']
        );
    }

    public function delete(Request $request){
        Edits::where('idEdits', $request['idEdits'])->delete();
        return $request['idEdits'];
    }

    public function update(Request $request)
    {
        DB::table('edits' )
            ->where('idEdits', '=' , $request['idEdits'])
            ->update(['OldModel' => $request['OldModel'], 'NewModel' => $request['NewModel']]);
        return $request['idEdits'];
    }

}

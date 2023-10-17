<?php

namespace App\Http\Controllers;

use App\Models\Trash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class TrashController extends Controller
{
    public function index()
    {
        $product = Trash::all();
        return json_decode(json_encode($product), true);
    }

    public function insert(Request $request): void
    {
        DB::table('trash')->upsert(
            [
                'trashString' => $request['trashString'],
            ],
            ['trashString'],
            ['trashString']
        );
    }

    public function delete(Request $request){
        Trash::where('idtrash', $request['idtrash'])->delete();
        return $request['idtrash'];
    }

    public function update(Request $request)
    {
        DB::table('trash' )
            ->where('idtrash', '=' , $request['idtrash'])
            ->update(['trashString' => $request['trashString']]);
        return $request['idtrash'];
    }

}

<?php

namespace App\Http\Controllers\Color;

use App\Http\Controllers\Controller;
use App\Http\Requests\ColorRequest;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ColorController extends Controller
{
    public function index(ColorRequest $request)
    {
        $data = $request->validated();
        return Color::create($data);
    }

    public function update(ColorRequest $request): string
    {
        DB::table('colors')
            ->where('idColor', $request['idColor'])
            ->update(
                [
                    'Color' => $request['Color']
                ]
            );
        return $request['idColor'];
    }
    public function delete(Request $request){
        Color::where('idColor',$request['idColor'])->delete();
    }
}

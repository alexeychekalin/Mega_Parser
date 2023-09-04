<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProviderRequest;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ProviderController extends Controller
{
    public function index(ProviderRequest $request)
    {
        $data = $request->validated();
        return Provider::create($data);
    }

    public function update(ProviderRequest $request): string
    {
        DB::table('providers')
            ->where('providerID', $request['providerID'])
            ->update(
                [
                    'providerName' => $request['providerName']
                ]
            );
        return $request['providerID'];
    }
    public function delete(Request $request){
        Provider::where('providerID',$request['providerID'])->delete();
    }
}

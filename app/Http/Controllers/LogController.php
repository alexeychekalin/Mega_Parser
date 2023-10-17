<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LogRequest;
use App\Http\Requests\User\UserRequest;
use App\Models\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class LogController extends Controller
{
    public function index(LogRequest $request)
    {
        $data = $request->validated();
        return Log::create($data);
    }

    public function readLogs(Request $request)
    {
        if($request['tab'] == 'one')
            $data = file('./home/mega_parser/log/SberParseApi.log');
        else
            $data = file('./home/mega_parser/log/TelegramApi.log');
        $data = array_reverse($data);
        return response()->json([
            'log' => $data
        ]);

    }
}

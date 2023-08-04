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
}

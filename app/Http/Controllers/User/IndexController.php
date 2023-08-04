<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class IndexController extends Controller
{
    public function index()
    {
        $user = User::all()->makeHidden('password');
        return json_decode(json_encode($user), true);
    }
}

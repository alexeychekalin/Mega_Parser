<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function index(UserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);
        return User::create($data);
    }

    public function update(UserRequest $request){
        if($request['password'] != 'null'){
            $data = $request->validated();
            $data['password'] = bcrypt($data['password']);
            DB::table('users')
                ->where('id', $request['id'])
                ->update(
                    [
                        'name' => DB::raw($data['name']),
                        'phone' => DB::raw($data['phone']),
                        'prefix' => DB::raw($data['prefix']),
                        'role' => DB::raw($data['role']),
                        'password' => $data['password']
                    ]
                );
            return $request['id'];
        }
        else{
            DB::table('users')
                ->where('id', $request['id'])
                ->update(
                    [
                        'name' =>DB::raw($request['name']),
                        'phone' => DB::raw($request['phone']),
                        'prefix' => DB::raw($request['prefix']),
                        'role' => DB::raw($request['role'])
                    ]
                );
            return $request['id'];

        }
    }
    public function delete(UserRequest $request){
        User::where('id',$request['id'])->delete();
    }

}

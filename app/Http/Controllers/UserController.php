<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private function responseFormat($data, $message, $httpCode) {
        if($data->count() == 0) {
            return response()->json([
                'code' => 404,
                'message' => "Data not Found",
                'data' => null
            ], 404);
        }else{
            return response()->json([
                'code' => $httpCode,
                'message' => $message,
                'data' => $data
            ], $httpCode);
        }
    }
    public function index(Request $request)
    {
        $this->validate($request, [
            'search' => 'string',
            'take' => 'required|numeric',
            'skip' => 'required|numeric',
        ]);
        $query = $request->get('search');
        $take = $request->take ?? 2;
        $skip = $request->skip ?? 0;
        if ($query != null) {
            $users = User::where('name', 'like', "%" . $query . "%")->skip($skip)->take($take);
        } else {
            $users = User::skip($skip)->take($take);
        }
        return $this->responseFormat($users->get(), 'Success', 200);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return $this->responseFormat($user, 'Success', 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
        ]);
        $user = User::create($request->all());
        return $this->responseFormat($user, 'Success stored', 201);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string',
        ]);
        $user = User::findOrFail($id);
        $user->update($request->all());
        return $this->responseFormat($user, 'Success updated', 200);
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return $this->responseFormat("deleted", 'Success deleted', 200);
    }
}

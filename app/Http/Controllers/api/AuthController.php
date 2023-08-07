<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;

class AuthController extends BaseController
{
    public function signin(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $authUser = Auth::user();
            $success['token'] = $authUser->createToken('MyAuthApp')->plainTextToken;
            $success['name'] = $authUser->name;

            return $this->sendResponse($success, 'User sign in');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised', 'new' => $request->email]);
        }
    }

    public function logout(Request $request)
    {
        // Revoke all tokens...
        // $user->tokens()->delete();
        // Revoke a specific token...
        // $user->tokens()->where('id', $tokenId)->delete();
        
        // Revoke the token that was used to authenticate the current request...
        $request->user()->currentAccessToken()->delete();
        return $this->sendResponse('', ['logout' => 'logout']);
    }

    public function signup(Request $request)
    {
        $validatior = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if($validatior->fails()){
            return $this->sendError('Error Validation', $validatior->errors());
        }

        $input = $request->all();   //get all the request
        $input['password'] = bcrypt($input['password']);
        
        $user = User::create($input);
        $success['token'] = $user->createToken('MyAuthApp')->plainTextToken;
        $success['name'] = $user->name;

        return $this->sendResponse($success, 'User has been created.');
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{

    public function register(RegisterUserRequest $request)
    {
        $request->validated();

        try {
            $user = User::create($request->all());

            $organisationName = $user->firstName . "'s Organisation";

            $user->organisations()->create([
                'name' => $organisationName
            ]);

            // Generate a JWT token for the user
            $token = JWTAuth::fromUser($user);

            if($user){
                return response()->json([
                    "status" => "success",
                    "message" => "Registration successful",
                    "data" => [
                        "accessToken" => $token,
                        "user" => new UserResource($user),
                    ],
                ], 201);
            }else{
                return response()->json([
                    "status" => "Bad request",
                    "message" => "Registration unsuccessful",
                    "statusCode" => 400,
                ], 400);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                "status" => "error",
                "message" => "Server error, try again",
                "statusCode" => 500,
            ], 500);
        }
    }

    public function login(LoginUserRequest $request)
    {

        $request->validated();

        if(!$token = auth()->attempt($request->only(['email', 'password']))){
            return response()->json([
                "status" => "Bad request",
                "message" => "Authentication failed",
                "statusCode" => 401,
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        return response()->json([
            "status" => "success",
            "message" => "Login successful",
            "data" => [
                "accessToken" => $token,
                "user" => new UserResource($user),
            ],
        ], 200);
    }

    public function logout(Request $request)
    {

        auth()->logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }
}

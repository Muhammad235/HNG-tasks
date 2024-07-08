<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

class UserController extends Controller
{

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $authUser = auth()->user();

$checkIfbelongsToOrg = OrganisationUser::where('orgId', $authUser->orgId)->where('userId', $user->id)->exists();

        if(!checkIfbelongsToOrg){

            return response()->json([
                "status" => "Bad Request",
                "message" => "Access denied, you can only get personal details",
                "data" => [
                    "user" => new UserResource($user),
                ],
            ], 400);
        }

        return response()->json([
            "status" => "success",
            "message" => "Request successful",
            "data" => [
                "user" => new UserResource($user),
            ],
        ], 200);
    }

}

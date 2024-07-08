<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\OrganisationUser;
use App\Models\Organisation;
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
        
        if ($authUser->id !== $user->id){
        
        
        $checkIfbelongsToOrg = OrganisationUser::where('orgId', $authUser->orgId)->where('userId', $user->id)->exists();
        $userOrg = Organisation::find($authUser->orgId); 

        if(!$checkIfbelongsToOrg){

            return response()->json([
                "status" => "Bad Request",
                "message" => "Access denied, user does not belong to your org",
               
            ], 400);
        }
        
        }

        return response()->json([
            "status" => "success",
            "message" => "Request successful",
            "data" =>new UserResource($user)
           
        ], 200);
    }

}
<?php

namespace App\Http\Controllers\Api\V1\Organisation;

use App\Models\User;
use App\Models\Organisation;
use Illuminate\Http\Request;
use App\Models\OrganisationUser;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrganisationResource;
use App\Http\Requests\StoreOrganisationRequest;

class OrganisationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        $organisations = $user->organisations;

        $organisationsBelongsTo = $user->userOrganisations;

        foreach ($organisationsBelongsTo as $organisationBelongsTo) {
            $org = Organisation::where('id', $organisationBelongsTo->orgId)->get();
        }
        $all = array_merge($org, $organisations);
        dd($all);

        if ($organisations) {
            $data = OrganisationResource::collection($organisations);
            return response()->json([
                "status" => "success",
                "message" => "Request successful",
                "data" => [
                    "organizations" => $data
                ],
            ], 200);
        }else {
            return response()->json([
                "status" => "error",
                "message" => "Server error, try again",
                "statusCode" => 500,
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrganisationRequest $request)
    {
        $request->validated();
        $user = auth()->user();

        dd($user);

        //create an organisation that belogs to the user making request
        $organisation = $user->organisations()->create($request->all());

        if ($organisation) {

            $data = new OrganisationResource($organisation);
            return response()->json([
                "status" => "success",
                "message" => "Organisation created successfully",
                "data" => $data,
            ], 201);
        }else {
            return response()->json([
                "status" => "error",
                "message" => "Server error, try again",
                "statusCode" => 500,
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Organisation $organisation)
    {
        $authUser = auth()->user();

        // Check if the user has created the organisation
        if ($organisation->userId !== $authUser->id) {

            // Check if the user belongs to the organisation
            $belongsToOrg = $authUser->userOrganisations()->where('userId', $organisation->userId)->where('orgId', $organisation->orgId)->exists();
            if (!$belongsToOrg) {
                return response()->json([
                    "status" => "Bad Request",
                    "message" => "You can't access an organisation you did not create or belong to",
                ], 400);
            }
        }
        $data = new OrganisationResource($organisation);

        return response()->json([
            "status" => "success",
            "message" => "Request successful",
            "data" => [
                "data" => $data,
            ],
        ], 200);
    }

  
    //Add user to organisation
    public function addUser(Request $request, string $orgId)
    {
        // Merge the organisation parameter into the request data
        $request->merge(['orgId' => $orgId]);

        // Validate the request with the merged orgId
        $request->validate([
            'userId' => 'required|exists:users,id',
            'orgId' => 'required|exists:organisations,id',
        ], [
            'userId.exists' => 'There is no record for this userId',
            'orgId.exists' => 'There is no record for this orgId',
        ]);

        $authUser = auth()->user();

        // Check if the user has created the organisation
        $organisation = Organisation::find($orgId);
        if ($organisation->userId !== $authUser->id) {
            // return response()->json([
            //     "status" => "Bad Request",
            //     "message" => "You can't add a user to an organisation you did not create or belong to",
            // ], 400);

            // Check if the user belongs to the organisation
            $belongsToOrg = $authUser->userOrganisations()->where('userId', $authUser->id)->where('orgId', $orgId)->exists();
            if (!$belongsToOrg) {
                return response()->json([
                    "status" => "Bad Request",
                    "message" => "You can't add a user to an organisation you don't belong to",
                ], 400);
            }
        }

        try {
            // Check if the user is already associated with the organisation
            $existingRecord = OrganisationUser::where('userId', $request->userId)
                ->where('orgId', $orgId)
                ->first();

            if ($existingRecord) {
                return response()->json([
                    "status" => "Bad Request",
                    "message" => "The user is already added to this organisation",
                ], 409);
            }

            //Add user to organisation
            OrganisationUser::create([
                'userId' => $request->userId,
                'orgId' => $orgId,
            ]);

            return response()->json([
                "status" => "success",
                "message" => "User added to organisation successfully",
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
            "error" => $e->getMessage(),
                "status" => "error",
                "message" => "An error occurred while adding the user to the organisation.",
            ], 500);
        }
    }

}

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Organisation\OrganisationController;


Route::prefix('auth')->group(function(){
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:api')->group(function() {
    // Route::post('logout', [AuthController::class, 'logout']);

    Route::get('users/{user}', [UserController::class, 'show']);

    Route::post('organisations', [OrganisationController::class, 'store']);
    Route::post('organisations/{orgId}', [OrganisationController::class, 'addUser']);
    Route::get('organisations', [OrganisationController::class, 'index']);
    Route::get('organisations/{organisation}', [OrganisationController::class, 'show']);
});


<?php

use Tymon\JWTAuth\JWTAuth;
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

Route::get('test', function(){

    $token ='eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2F1dGgvbG9naW4iLCJpYXQiOjE3MjA0MTc2NjksImV4cCI6MTcyMDQyMTI2OSwibmJmIjoxNzIwNDE3NjY5LCJqdGkiOiJqYUlhRkgySnoxMFBWQkJ0Iiwic3ViIjoiOWM3ODk3Y2EtYzMwMy00Y2UyLWJkNDYtNDU4M2QxYjNjN2Y4IiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.NLR0WZPxO0XZ-FIW2pQ8zt6KgQyBi7_I4JV1juV2-2w';

    $claims = JWTAuth::setToken($token)->getPayload();
});
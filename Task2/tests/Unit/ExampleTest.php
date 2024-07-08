<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

//End-to-End Test

it('Should Register User Successfully and Verify the organisation name', function(){
    $user = [
        "firstName" => "John",
        "lastName" => "Doe",
        "email" => "John@gamil.com",
        "password" => Hash::make('password'),
    ];

    $organisationName = $user['firstName'] . "'s Organisation";

    $response = $this->postJson('/api/auth/register', $user);
    $response->assertStatus(201);

    $this->assertDatabaseHas('users', [
        'email' => $user['email']
    ]);

    $this->assertDatabaseHas('organisations', [
        'name' => $organisationName,
    ]);
});

it('Should fail if the firstName field is missing when registering', function(){
    $user = [
        //missing firstName
        "lastName" => "Doe",
        "email" => "John@gamil.com",
        "password" => Hash::make('password'),
    ];
    $response = $this->postJson('/api/auth/register', $user);
    $response->assertStatus(422);
});

it('Should fail if the lastName field is missing when registering', function(){
    $user = [
        "firstName" => "John",
        //missing lastName
        "email" => "John@gamil.com",
        "password" => Hash::make('password'),
    ];
    $response = $this->postJson('/api/auth/register', $user);
    $response->assertStatus(422);
});


it('Should fail if the email field is missing when registering', function(){
    $user = [
        "firstName" => "John",
        "lastName" => "Doe",
        //missing email
        "password" => Hash::make('password'),
    ];
    $response = $this->postJson('/api/auth/register', $user);
    $response->assertStatus(422);
});

it('Should fail if the password field is missing when registering', function(){
    $user = [
        "firstName" => "John",
        "lastName" => "Doe",
        "email" => "John@gamil.com"
        //missing password
    ];
    $response = $this->postJson('/api/auth/register', $user);
    $response->assertStatus(422);
});



it("Should fail if there's duplicate email", function(){
    $user = [
        "firstName" => "John",
        "lastName" => "Doe",
        "email" => "John@gamil.com",
        "password" => Hash::make('password'),
    ];

    $response1 = $this->postJson('/api/auth/register', $user);
    $response1->assertStatus(201);

    $response2 = $this->postJson('/api/auth/register', $user);
    $response2->assertStatus(422);
});

// it('Should log the user in successfully', function(){
//     $user = [
//         "email" => "John@gamil.com",
//         "password" => 'password',
//     ];
//     $response = $this->postJson('/api/auth/login', $user);
//     $response->assertStatus(200);
// });


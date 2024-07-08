<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);


//Unit Test Cases

it('Ensure Token is generated', function(){
    $user = [
        "firstName" => "John",
        "lastName" => "Doe",
        "email" => "John@gamil.com",
        "password" => Hash::make('password'),
    ];

    $response = $this->postJson('/api/auth/register', $user);
    $response->assertStatus(201);

    expect($response['data']['accessToken'])->not()->toBeEmpty();
});


//End-to-End Test

it('Should Register User Successfully with default organisation', function(){
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
        'name' => $organisationName
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
    $response->assertSee("The first name field is required.");
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
    $response->assertSee("The last name field is required.");
});


it('Should fail if the email field is missing when registering', function(){
    $user = [
        "firstName" => "John",
        "lastName" => "Doe",
        //missing email
        "password" => Hash::make('password'),
    ];
    $response = $this->postJson('/api/auth/register', $user);
    $response->assertStatus(422);#
    $response->assertSee("The email field is required.");
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
    $response->assertSee("The password field is required.");
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

it('Should log the user in successfully', function(){
    $user = [
        "firstName" => "John",
        "lastName" => "Doe",
        "email" => "John@gamil.com",
        "password" => Hash::make('password'),
    ];

    $response = $this->postJson('/api/auth/register', $user);

    $user = [
        "email" => "John@gamil.com",
        "password" => 'password',
    ];
    $response = $this->postJson('/api/auth/login', $user);
    $response->assertStatus(200);
});


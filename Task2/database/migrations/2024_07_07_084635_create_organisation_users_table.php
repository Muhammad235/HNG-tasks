<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organisation_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('userId')->constrained('users', 'id');
            $table->foreignUuid('orgId')->constrained('organisations', 'id');
            $table->unique(['userId', 'orgId']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisation_users');
    }
};

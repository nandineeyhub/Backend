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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->unique();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->boolean('userType')->default(1);
            $table->enum('status',['Active','Inactive'])->default('Active');
            $table->string('collegeCode')->unique()->nullable();
            $table->string('address')->nullable();
            $table->string('stateID')->nullable();
            $table->string('countryID')->nullable();
            $table->string('cityID')->nullable();
            $table->string('contactPerson')->nullable();
            $table->string('phoneNo')->unique()->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

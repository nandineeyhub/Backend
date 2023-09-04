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
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->integer('clientID');
            $table->enum('step',['Lead', 'HotLead', 'Client'])->default('Lead');
            $table->string('email')->unique();
            $table->string('name');
            $table->text('message');
            $table->date('enquiryDate');
            $table->string('phone');
            $table->text('address');
            $table->enum('status',['Active','Inactive'])->default('Active');
            $table->string('course');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};

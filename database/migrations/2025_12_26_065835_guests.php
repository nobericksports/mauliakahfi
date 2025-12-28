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
        // database/migrations/xxxx_create_guests_table.php
Schema::create('guests', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('city')->nullable();
    $table->string('guest_code')->unique();
    $table->boolean('is_rsvp')->default(false);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

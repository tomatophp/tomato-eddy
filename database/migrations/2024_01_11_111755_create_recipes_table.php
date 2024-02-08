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
        Schema::create('recipes', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->string('name')->index();
            $table->string('description')->nullable();
            $table->string('user')->default('root')->nullable();
            $table->string('type')->default('software')->nullable();
            $table->json('script')->nullable();
            $table->string('view')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('recipes');
    }
};

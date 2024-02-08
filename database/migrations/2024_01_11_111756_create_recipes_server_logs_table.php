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
        Schema::create('recipes_server_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignIdFor(\TomatoPHP\TomatoEddy\Models\Server::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\TomatoPHP\TomatoEddy\Models\Recipe::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\TomatoPHP\TomatoEddy\Models\Task::class)->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('recipes_server_logs');
    }
};

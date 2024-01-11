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
       Schema::table('crons', function (Blueprint $table) {
            $table->foreignIdFor(\TomatoPHP\TomatoEddy\Models\Site::class)->nullable()->constrained()->cascadeOnDelete();
       });

        Schema::table('daemons', function (Blueprint $table) {
            $table->foreignIdFor(\TomatoPHP\TomatoEddy\Models\Site::class)->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::table('databases', function (Blueprint $table) {
            $table->foreignIdFor(\TomatoPHP\TomatoEddy\Models\Site::class)->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::table('database_users', function (Blueprint $table) {
            $table->foreignIdFor(\TomatoPHP\TomatoEddy\Models\Site::class)->nullable()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropColumns('crons', ['site_id']);
        Schema::dropColumns('daemons', ['site_id']);
        Schema::dropColumns('databases', ['site_id']);
        Schema::dropColumns('database_users', ['site_id']);
    }
};

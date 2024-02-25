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
        Schema::table('site_templates', function (Blueprint $table) {
            $table->longText('queue_command')->nullable();
            $table->longText('schedule_command')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_templates', function (Blueprint $table) {
            $table->dropColumn('queue_command');
            $table->dropColumn('schedule_command');
        });
    }
};

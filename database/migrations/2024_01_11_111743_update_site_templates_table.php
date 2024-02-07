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
            $table->boolean('has_server')->default(false)->nullable();
            $table->string('server_name')->nullable();
            $table->string('server_credentials_id')->nullable();
            $table->string('server_custom_server')->nullable();
            $table->string('server_ssh_keys')->nullable();
            $table->string('server_type')->nullable();
            $table->string('server_region')->nullable();
            $table->string('server_image')->nullable();
            $table->string('domain')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_templates', function (Blueprint $table) {
            $table->dropColumn('has_server');
            $table->dropColumn('server_name');
            $table->dropColumn('server_credentials_id');
            $table->dropColumn('server_custom_server');
            $table->dropColumn('server_ssh_keys');
            $table->dropColumn('server_type');
            $table->dropColumn('server_region');
            $table->dropColumn('server_image');
            $table->dropColumn('domain');
        });
    }
};

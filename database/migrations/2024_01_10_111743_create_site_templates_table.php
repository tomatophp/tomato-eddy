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
        Schema::create('site_templates', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name')->index();
            $table->string('type')->index();
            $table->boolean('zero_downtime_deployment')->default(true);
            $table->string('repository_url')->nullable();
            $table->string('repository_branch')->nullable();
            $table->string('web_folder');
            $table->string('php_version')->nullable()->index();
            $table->longText('hook_before_updating_repository')->nullable();
            $table->longText('hook_after_updating_repository')->nullable();
            $table->longText('hook_before_making_current')->nullable();
            $table->longText('hook_after_making_current')->nullable();
            $table->boolean('add_server_ssh_key_to_github')->default(false);
            $table->boolean('add_dns_zone_to_cloudflare')->default(false);
            $table->boolean('has_queue')->default(false)->nullable();
            $table->boolean('has_schedule')->default(false)->nullable();
            $table->boolean('has_database')->default(false)->nullable();
            $table->string('database_name')->nullable();
            $table->string('database_user')->nullable();
            $table->string('database_password')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_templates');
    }
};

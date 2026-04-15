<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * `users` is created before module tables; FKs are added here once `cities` and `services` exist.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('city_id')
                ->references('id')
                ->on('cities')
                ->nullOnDelete();

            $table->foreign('service_id')
                ->references('id')
                ->on('services')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            $table->dropForeign(['service_id']);
        });
    }
};

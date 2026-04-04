<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('package_subscription_snapshots', function (Blueprint $table) {
            $table->foreignId('source_package_id')
                ->nullable()
                ->after('package_subscription_id')
                ->constrained('packages')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('package_subscription_snapshots', function (Blueprint $table) {
            $table->dropConstrainedForeignId('source_package_id');
        });
    }
};

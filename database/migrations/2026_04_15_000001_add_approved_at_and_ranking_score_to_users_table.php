<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('approved_at')->nullable()->after('welcome_free_package_granted_at');
            $table->decimal('ranking_score', 8, 4)->default(0)->after('approved_at');

            $table->index('ranking_score');
            $table->index('approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['ranking_score']);
            $table->dropIndex(['approved_at']);
            $table->dropColumn(['approved_at', 'ranking_score']);
        });
    }
};

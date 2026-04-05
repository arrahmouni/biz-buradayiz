<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('verimor_call_events', function (Blueprint $table) {
            $table->string('caller_number_normalized', 32)->nullable();
            $table->index(['user_id', 'caller_number_normalized'], 'verimor_call_events_user_caller_idx');
        });
    }

    public function down(): void
    {
        Schema::table('verimor_call_events', function (Blueprint $table) {
            $table->dropIndex('verimor_call_events_user_caller_idx');
            $table->dropColumn('caller_number_normalized');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verimor_call_events', function (Blueprint $table) {
            $table->id();
            $table->string('call_uuid')->unique();
            $table->string('event_type', 32)->nullable();
            $table->string('direction', 32)->nullable();
            $table->string('destination_number_normalized', 32)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('package_subscription_id')->nullable()->constrained('package_subscriptions')->nullOnDelete();
            $table->boolean('answered')->default(false);
            $table->boolean('consumed_quota')->default(false);
            $table->json('raw_payload');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verimor_call_events');
    }
};

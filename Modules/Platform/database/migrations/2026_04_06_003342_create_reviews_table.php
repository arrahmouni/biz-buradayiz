<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Platform\Enums\ReviewStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('verimor_call_event_id')->nullable()->unique()->constrained('verimor_call_events')->nullOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->string('status', 32)->default(ReviewStatus::Pending->value);
            $table->text('body')->nullable();
            $table->string('reviewer_display_name')->nullable();
            $table->string('reviewer_phone_normalized', 32)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

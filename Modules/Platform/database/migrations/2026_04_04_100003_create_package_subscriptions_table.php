<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Auth\Models\User;
use Modules\Platform\Enums\PackageSubscriptionStatus;
use Modules\Platform\Models\Package;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Package::class)->constrained()->cascadeOnDelete();
            $table->enum('status', PackageSubscriptionStatus::values())->default(PackageSubscriptionStatus::Active->value);
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->unsignedInteger('remaining_connections')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_subscriptions');
    }
};

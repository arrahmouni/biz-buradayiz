<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Auth\Models\User;
use Modules\Platform\Enums\PackageSubscriptionPaymentMethod;
use Modules\Platform\Enums\PackageSubscriptionPaymentStatus;
use Modules\Platform\Enums\PackageSubscriptionStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->enum('status', PackageSubscriptionStatus::values())->default(PackageSubscriptionStatus::Active->value);
            $table->enum('payment_status', PackageSubscriptionPaymentStatus::values())->default(PackageSubscriptionPaymentStatus::Pending->value);
            $table->enum('payment_method', PackageSubscriptionPaymentMethod::values())->default(PackageSubscriptionPaymentMethod::BankTransfer->value);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('admin_notes')->nullable();
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

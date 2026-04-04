<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Platform\Enums\BillingPeriod;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->decimal('price', 12, 2)->default(0);
            $table->char('currency', 3)->default('TRY');
            $table->enum('billing_period', BillingPeriod::values())->default(BillingPeriod::Monthly->value);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->disableable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};

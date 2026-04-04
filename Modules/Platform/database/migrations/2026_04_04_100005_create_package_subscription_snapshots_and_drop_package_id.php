<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Platform\Models\PackageSubscription;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_subscription_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PackageSubscription::class)->constrained()->cascadeOnDelete();
            $table->json('name_translations');
            $table->decimal('price', 15, 2);
            $table->string('currency', 3);
            $table->string('billing_period', 32);
            $table->unsignedInteger('connections_count');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('package_subscription_snapshots');
    }
};

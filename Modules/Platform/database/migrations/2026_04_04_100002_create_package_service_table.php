<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Platform\Models\Package;
use Modules\Platform\Models\Service;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_service', function (Blueprint $table) {
            $table->foreignIdFor(Package::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Service::class)->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['package_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_service');
    }
};

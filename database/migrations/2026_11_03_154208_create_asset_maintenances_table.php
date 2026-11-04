<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained()->nullOnDelete();
            $table->string('pic_name');
            $table->string('asset_name');
            $table->string('brand_type');
            $table->text('damage_description');
            $table->string('previously_serviced'); // 'sudah' atau 'belum'
            $table->date('service_date');
            $table->text('service_location');
            $table->string('condition_photo_path');
            $table->string('invoice_photo_path');
            $table->string('status')->default('selesai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_maintenances');
    }
};

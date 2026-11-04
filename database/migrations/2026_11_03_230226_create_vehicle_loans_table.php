<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_loans', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('pic_name');
            $table->string('driver_name');
            $table->date('departure_date');
            $table->date('return_date');
            $table->string('departure_km');
            $table->string('vehicle_type');
            $table->text('purpose');
            $table->string('cleanliness_condition');
            $table->text('suggestions')->nullable();
            $table->string('departure_speedometer_photo_path');
            $table->string('return_speedometer_photo_path');
            $table->string('status')->default('selesai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_loans');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fatigue_assessments', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->date('assessment_date');
            $table->string('recent_illness'); // Ya / Tidak
            $table->string('medication_drowsy'); // Ya / Tidak
            $table->string('personal_distraction'); // Ya / Tidak
            $table->string('sleep_hours'); // kurang dari 6 jam / lebih dari 6 jam
            $table->string('fit_to_drive'); // Ya / Tidak
            $table->string('status')->default('Selesai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fatigue_assessments');
    }
};

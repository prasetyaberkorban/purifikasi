<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_leaves', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('leave_type');
            $table->unsignedInteger('days_count');
            $table->text('description');
            $table->string('attachment_path');
            $table->string('attachment_name');
            $table->unsignedBigInteger('attachment_size')->default(0);
            $table->string('status')->default('diajukan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_leaves');
    }
};

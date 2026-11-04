<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('code', 48)->unique();
            $table->string('name');
            $table->string('serial_number')->nullable();
            $table->string('brand')->nullable();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->string('room')->nullable();
            $table->string('owner_unit', 64)->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('minimum_stock')->default(1);
            $table->unsignedInteger('borrowed_quantity')->default(0);
            $table->string('status', 16)->default('tersedia');
            $table->string('condition', 24)->default('belum_dicek');
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->string('photo_path')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'condition']);
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};

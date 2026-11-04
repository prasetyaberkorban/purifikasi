<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_procurements', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('pic_name');
            $table->date('realization_date');
            $table->string('item_name');
            $table->string('brand_type');
            $table->string('quantity_size');
            $table->text('store_name');
            $table->string('proof_file_path');
            $table->string('proof_file_name');
            $table->string('proof_file_type')->nullable();
            $table->unsignedBigInteger('proof_file_size')->default(0);
            $table->string('status')->default('selesai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_procurements');
    }
};

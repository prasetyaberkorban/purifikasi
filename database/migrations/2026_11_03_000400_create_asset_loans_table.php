<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_loans', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('borrower_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('purpose')->nullable();
            $table->string('room')->nullable();
            $table->timestamp('borrowed_at');
            $table->date('due_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->string('status', 16)->default('dipinjam');
            $table->string('condition_on_return', 24)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['status', 'borrowed_at']);
            $table->index('borrower_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_loans');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('spk_number');
            $table->date('spk_date');
            $table->string('project_name');
            $table->string('second_party_name');
            $table->text('job_description');
            $table->decimal('contract_value', 15, 2);
            $table->unsignedInteger('duration_months')->default(1);
            $table->decimal('termin_1', 6, 2)->nullable()->default(0);
            $table->decimal('termin_2', 6, 2)->nullable()->default(0);
            $table->decimal('termin_3', 6, 2)->nullable()->default(0);
            $table->string('bank_name');
            $table->string('bank_account_number');
            $table->string('bank_account_holder');
            $table->text('contract_notes')->nullable();
            $table->text('source_document_text')->nullable();
            $table->string('source_document_path')->nullable();
            $table->string('source_document_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_contracts');
    }
};

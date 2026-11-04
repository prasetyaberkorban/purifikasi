<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_leaves', function (Blueprint $table) {
            $table->string('attachment_path')->nullable()->change();
            $table->string('attachment_name')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('employee_leaves', function (Blueprint $table) {
            $table->string('attachment_path')->nullable(false)->change();
            $table->string('attachment_name')->nullable(false)->change();
        });
    }
};

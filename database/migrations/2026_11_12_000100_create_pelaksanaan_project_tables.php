<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Matriks Pelaksanaan (17 Proyek Awal)
        if (!Schema::hasTable('pelaksanaan_projects')) {
            Schema::create('pelaksanaan_projects', function (Blueprint $table) {
                $table->id();
                $table->string('kode_project', 64)->nullable();
                $table->text('nama_project');
                $table->string('pengelola', 64)->default('Semarang');
                $table->string('status_pekerjaan', 128);
                $table->decimal('progress_pekerjaan', 4, 2)->default(0);
                $table->string('pic', 128)->nullable();
                $table->date('kontrak_awal')->nullable();
                $table->date('kontrak_akhir')->nullable();
                $table->integer('remaining_days')->nullable();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->timestamps();

                $table->index('pengelola');
                $table->index('status_pekerjaan');
                $table->index('pic');
            });
        }

        // 2. Referensi Pelaksanaan: Tahap Pelaksanaan
        if (!Schema::hasTable('pelaksanaan_ref_tahap')) {
            Schema::create('pelaksanaan_ref_tahap', function (Blueprint $table) {
                $table->id();
                $table->string('tahap', 128);
                $table->decimal('bobot', 4, 2)->default(0);
                $table->integer('urutan')->default(0);
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }

        // 3. Referensi Pelaksanaan: Master PIC
        if (!Schema::hasTable('pelaksanaan_ref_pic')) {
            Schema::create('pelaksanaan_ref_pic', function (Blueprint $table) {
                $table->id();
                $table->string('nama', 128)->unique();
                $table->timestamps();
            });
        }

        // 4. Petunjuk Pra-Kontrak: Panduan Penggunaan
        if (!Schema::hasTable('pelaksanaan_guide_panduan')) {
            Schema::create('pelaksanaan_guide_panduan', function (Blueprint $table) {
                $table->id();
                $table->string('bagian_warna', 128);
                $table->string('fungsi', 255);
                $table->text('cara_penggunaan');
                $table->timestamps();
            });
        }

        // 5. Petunjuk Pra-Kontrak: Standar Bobot Tahap Proses Detail
        if (!Schema::hasTable('pelaksanaan_guide_bobot_proses')) {
            Schema::create('pelaksanaan_guide_bobot_proses', function (Blueprint $table) {
                $table->id();
                $table->string('tahap', 128);
                $table->decimal('bobot', 4, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelaksanaan_guide_bobot_proses');
        Schema::dropIfExists('pelaksanaan_guide_panduan');
        Schema::dropIfExists('pelaksanaan_ref_pic');
        Schema::dropIfExists('pelaksanaan_ref_tahap');
        Schema::dropIfExists('pelaksanaan_projects');
    }
};

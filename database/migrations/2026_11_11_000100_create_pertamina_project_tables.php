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
        // 1. Matriks Pra-Kontrak
        if (!Schema::hasTable('pertamina_projects')) {
            Schema::create('pertamina_projects', function (Blueprint $table) {
                $table->id();
                $table->string('kode_project', 64)->unique();
                $table->text('nama_project');
                $table->decimal('nilai_project', 15, 2)->default(0);
                $table->string('client', 255)->default('');
                $table->string('partner', 128)->nullable();
                $table->string('pengelola', 64)->default('Semarang');
                $table->string('progress_penawaran', 128)->nullable();
                $table->decimal('progress_pra_kontrak', 4, 2)->nullable()->default(0);
                $table->string('hasil_penawaran', 64)->nullable()->default('Dalam Proses');
                $table->string('status_pekerjaan', 128)->nullable();
                $table->decimal('progress_pekerjaan', 4, 2)->nullable();
                $table->string('pic', 128)->nullable();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->timestamps();

                $table->index('kode_project');
                $table->index('hasil_penawaran');
                $table->index('pengelola');
            });
        }

        // 2. Referensi Pra-Kontrak: Tahap Pra-Kontrak
        if (!Schema::hasTable('pertamina_ref_tahap_pra_kontrak')) {
            Schema::create('pertamina_ref_tahap_pra_kontrak', function (Blueprint $table) {
                $table->id();
                $table->string('tahap', 128);
                $table->decimal('bobot', 4, 2)->default(0);
                $table->integer('urutan')->default(0);
                $table->timestamps();
            });
        }

        // 3. Referensi Pra-Kontrak: Hasil Penawaran
        if (!Schema::hasTable('pertamina_ref_hasil_penawaran')) {
            Schema::create('pertamina_ref_hasil_penawaran', function (Blueprint $table) {
                $table->id();
                $table->string('nama', 64);
                $table->timestamps();
            });
        }

        // 4. Referensi Pra-Kontrak: Status Proses
        if (!Schema::hasTable('pertamina_ref_status_proses')) {
            Schema::create('pertamina_ref_status_proses', function (Blueprint $table) {
                $table->id();
                $table->string('nama', 128);
                $table->timestamps();
            });
        }

        // 5. Referensi Pelaksanaan: Tahap Pelaksanaan
        if (!Schema::hasTable('pertamina_ref_tahap_pelaksanaan')) {
            Schema::create('pertamina_ref_tahap_pelaksanaan', function (Blueprint $table) {
                $table->id();
                $table->string('tahap', 128);
                $table->decimal('bobot', 4, 2)->default(0);
                $table->integer('urutan')->default(0);
                $table->timestamps();
            });
        }

        // 6. Referensi Pelaksanaan: Master PIC
        if (!Schema::hasTable('pertamina_ref_pic')) {
            Schema::create('pertamina_ref_pic', function (Blueprint $table) {
                $table->id();
                $table->string('nama', 128)->unique();
                $table->timestamps();
            });
        }

        // 7. Petunjuk Pra-Kontrak: Panduan Penggunaan
        if (!Schema::hasTable('pertamina_guide_panduan')) {
            Schema::create('pertamina_guide_panduan', function (Blueprint $table) {
                $table->id();
                $table->string('bagian_warna', 128);
                $table->string('fungsi', 255);
                $table->text('cara_penggunaan');
                $table->timestamps();
            });
        }

        // 8. Petunjuk Pra-Kontrak: Standar Bobot Tahap Proses
        if (!Schema::hasTable('pertamina_guide_bobot_proses')) {
            Schema::create('pertamina_guide_bobot_proses', function (Blueprint $table) {
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
        Schema::dropIfExists('pertamina_guide_bobot_proses');
        Schema::dropIfExists('pertamina_guide_panduan');
        Schema::dropIfExists('pertamina_ref_pic');
        Schema::dropIfExists('pertamina_ref_tahap_pelaksanaan');
        Schema::dropIfExists('pertamina_ref_status_proses');
        Schema::dropIfExists('pertamina_ref_hasil_penawaran');
        Schema::dropIfExists('pertamina_ref_tahap_pra_kontrak');
        Schema::dropIfExists('pertamina_projects');
    }
};

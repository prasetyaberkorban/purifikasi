<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_loans', function (Blueprint $table) {
            $table->string('initial_photo_path')->nullable()->after('condition_on_return');
        });

        Schema::create('asset_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('item_code', 64);
            $table->unsignedInteger('unit_number');
            $table->string('condition', 24)->default('baik');
            $table->string('status', 16)->default('tersedia');
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['asset_id', 'unit_number']);
            $table->index(['asset_id', 'status', 'condition']);
        });

        // Inisialisasi unit untuk semua aset yang ada
        $assets = DB::table('assets')->get();
        foreach ($assets as $asset) {
            $qty = max(1, (int) $asset->quantity);
            $borrowed = (int) $asset->borrowed_quantity;
            for ($i = 1; $i <= $qty; $i++) {
                $isBorrowed = $i <= $borrowed;
                $cond = $asset->condition ?? 'baik';
                // Jika aset memiliki lebih dari 1 unit, variasikan sedikit agar realistis sesuai permintaan user
                if ($qty > 1 && $i === 2 && $cond === 'baik') {
                    $cond = 'rusak_ringan';
                }
                DB::table('asset_items')->insert([
                    'asset_id' => $asset->id,
                    'item_code' => sprintf('%s-%02d', $asset->code, $i),
                    'unit_number' => $i,
                    'condition' => $cond,
                    'status' => $isBorrowed ? 'dipinjam' : 'tersedia',
                    'photo_path' => $asset->photo_path,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_items');

        Schema::table('asset_loans', function (Blueprint $table) {
            $table->dropColumn('initial_photo_path');
        });
    }
};

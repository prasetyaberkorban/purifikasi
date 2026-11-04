<?php

namespace Tests\Feature;

use App\Models\AssetProcurement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssetProcurementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
    }

    public function test_user_can_view_procurement_form(): void
    {
        $user = User::query()->first();

        $response = $this->actingAs($user)->get(route('procurements.create'));

        $response->assertOk();
        $response->assertSee('Formulir Realisasi Pengadaan dan Aset Kantor');
        $response->assertSee('1. PIC');
        $response->assertSee('2. Tanggal Realisasi');
        $response->assertSee('3. Nama Barang');
        $response->assertSee('4. Merk Type');
        $response->assertSee('5. Jumlah / Ukuran');
        $response->assertSee('6. Nama Toko (Isi link jika beli online)');
        $response->assertSee('7. Bukti Realisasi');
    }

    public function test_submitting_procurement_form_validates_required_fields(): void
    {
        $user = User::query()->first();

        $response = $this->actingAs($user)->post(route('procurements.store'), []);

        $response->assertSessionHasErrors([
            'pic_name',
            'realization_date',
            'item_name',
            'brand_type',
            'quantity_size',
            'store_name',
            'proof_file',
        ]);
    }

    public function test_user_can_submit_procurement_form_with_valid_file(): void
    {
        $user = User::query()->first();
        $pdfFile = UploadedFile::fake()->create('nota_transaksi.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->post(route('procurements.store'), [
            'pic_name' => 'Ahmad Faisal',
            'realization_date' => now()->toDateString(),
            'item_name' => 'Monitor LED 24 Inch',
            'brand_type' => 'LG 24MP400-B',
            'quantity_size' => '3 Unit / 24 Inch',
            'store_name' => 'https://tokopedia.com/toko-komputer/lg-24mp400',
            'proof_file' => $pdfFile,
        ]);

        $this->assertDatabaseHas('asset_procurements', [
            'pic_name' => 'Ahmad Faisal',
            'item_name' => 'Monitor LED 24 Inch',
            'brand_type' => 'LG 24MP400-B',
            'quantity_size' => '3 Unit / 24 Inch',
            'proof_file_name' => 'nota_transaksi.pdf',
        ]);

        $procurement = AssetProcurement::query()->first();
        $response->assertRedirect(route('procurements.show', $procurement));

        Storage::disk('public')->assertExists($procurement->proof_file_path);
    }

    public function test_user_can_download_proof_file(): void
    {
        $user = User::query()->first();
        $pdfFile = UploadedFile::fake()->create('invoice.xlsx', 200, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->actingAs($user)->post(route('procurements.store'), [
            'pic_name' => 'Siti Nurhaliza',
            'realization_date' => now()->toDateString(),
            'item_name' => 'Kertas HVS A4',
            'brand_type' => 'PaperOne 80gr',
            'quantity_size' => '10 Rim',
            'store_name' => 'Toko Alat Tulis Depok',
            'proof_file' => $pdfFile,
        ]);

        $procurement = AssetProcurement::query()->first();

        $response = $this->actingAs($user)->get(route('procurements.download', $procurement));
        $response->assertOk();
    }
}

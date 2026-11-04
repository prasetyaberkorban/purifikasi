<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetMaintenance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssetMaintenanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
    }

    public function test_user_can_view_service_form(): void
    {
        $user = User::query()->first();

        $response = $this->actingAs($user)->get(route('services.create'));

        $response->assertOk();
        $response->assertSee('Formulir Service dan Pemeliharaan Aset Kantor');
        $response->assertSee('Nama Pengguna PIC');
        $response->assertSee('Dokumentasi Kondisi Aset');
        $response->assertSee('Nota Service / Pemeliharaan');
    }

    public function test_submitting_service_form_validates_required_fields(): void
    {
        $user = User::query()->first();

        $response = $this->actingAs($user)->post(route('services.store'), []);

        $response->assertSessionHasErrors([
            'pic_name',
            'asset_name',
            'brand_type',
            'damage_description',
            'previously_serviced',
            'service_date',
            'service_location',
            'condition_photo',
            'invoice_photo',
        ]);
    }

    public function test_user_can_submit_service_form_with_all_fields(): void
    {
        $user = User::query()->first();
        $asset = Asset::query()->first();

        $conditionPhoto = UploadedFile::fake()->image('kondisi.jpg');
        $invoicePhoto = UploadedFile::fake()->image('nota.jpg');

        $response = $this->actingAs($user)->post(route('services.store'), [
            'pic_name' => 'Budi Santoso',
            'asset_id' => $asset->id,
            'asset_name' => $asset->name,
            'brand_type' => 'Daikin Inverter',
            'damage_description' => 'AC tidak dingin dan freon bocor.',
            'previously_serviced' => 'sudah',
            'service_date' => now()->toDateString(),
            'service_location' => 'Bengkel AC Jaya, Depok',
            'condition_photo' => $conditionPhoto,
            'invoice_photo' => $invoicePhoto,
        ]);

        $this->assertDatabaseHas('asset_maintenances', [
            'pic_name' => 'Budi Santoso',
            'asset_name' => $asset->name,
            'brand_type' => 'Daikin Inverter',
            'previously_serviced' => 'sudah',
            'service_location' => 'Bengkel AC Jaya, Depok',
        ]);

        $maintenance = AssetMaintenance::query()->first();
        $response->assertRedirect(route('services.show', $maintenance));

        Storage::disk('public')->assertExists($maintenance->condition_photo_path);
        Storage::disk('public')->assertExists($maintenance->invoice_photo_path);
    }
}

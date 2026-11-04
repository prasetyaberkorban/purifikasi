<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VehicleLoan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VehicleLoanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
    }

    public function test_user_can_view_vehicle_loan_form(): void
    {
        $user = User::query()->first();

        $response = $this->actingAs($user)->get(route('vehicle-loans.create'));

        $response->assertOk();
        $response->assertSee('Formulir Peminjaman Kendaraan');
        $response->assertSee('Formulir ini digunakan pada saat Personil/Tim melakukan peminjaman kendaraan untuk kepentingan perusahaan dan/atau pribadi, dengan catatan menginformasikan kepada penanggung jawab kendaraan.');
        $response->assertSee('1. PIC');
        $response->assertSee('2. Nama Driver');
        $response->assertSee('3. Tanggal Pergi');
        $response->assertSee('4. Tanggal Kembali');
        $response->assertSee('5. Kilometer (Km) Berangkat (ketik)');
        $response->assertSee('6. Jenis Kendaraan');
        $response->assertSee('Innova Reborn');
        $response->assertSee('Ertiga');
        $response->assertSee('Ignis');
        $response->assertSee('Xenia');
        $response->assertSee('Yang lain:');
        $response->assertSee('7. Tujuan Peminjaman');
        $response->assertSee('8. Kondisi Kebersihan Kendaraan');
        $response->assertSee('Sampah sudah dibersihkan');
        $response->assertSee('Sampah masih tersisa di dalam mobil');
        $response->assertSee('9. Saran dan Masukan');
        $response->assertSee('10. Foto Speedometer Berangkat (Dilengkapi Timestamp)');
        $response->assertSee('11. Foto Speedometer Kembali (Dilengkapi Timestamp)');
    }

    public function test_submitting_vehicle_loan_validates_required_fields(): void
    {
        $user = User::query()->first();

        $response = $this->actingAs($user)->post(route('vehicle-loans.store'), []);

        $response->assertSessionHasErrors([
            'pic_name',
            'driver_name',
            'departure_date',
            'return_date',
            'departure_km',
            'vehicle_type_choice',
            'purpose',
            'cleanliness_choice',
            'departure_speedometer_photo',
            'return_speedometer_photo',
        ]);
    }

    public function test_user_can_submit_vehicle_loan_with_valid_data(): void
    {
        $user = User::query()->first();

        $depPhoto = UploadedFile::fake()->image('speedo_dep.jpg');
        $retPhoto = UploadedFile::fake()->image('speedo_ret.jpg');

        $response = $this->actingAs($user)->post(route('vehicle-loans.store'), [
            'pic_name' => 'Budi Santoso',
            'driver_name' => 'Ahmad Driver',
            'departure_date' => '2026-11-04',
            'return_date' => '2026-11-05',
            'departure_km' => '45.200',
            'vehicle_type_choice' => 'Innova Reborn',
            'purpose' => 'Kunjungan kerja ke cabang Surabaya',
            'cleanliness_choice' => 'sampah sudah dibersihkan',
            'suggestions' => 'AC sangat dingin, kondisi prima.',
            'departure_speedometer_photo' => $depPhoto,
            'return_speedometer_photo' => $retPhoto,
        ]);

        $this->assertDatabaseHas('vehicle_loans', [
            'pic_name' => 'Budi Santoso',
            'driver_name' => 'Ahmad Driver',
            'vehicle_type' => 'Innova Reborn',
            'departure_km' => '45.200',
            'cleanliness_condition' => 'sampah sudah dibersihkan',
            'suggestions' => 'AC sangat dingin, kondisi prima.',
        ]);

        $loan = VehicleLoan::first();
        $this->assertNotNull($loan);
        $response->assertRedirect(route('vehicle-loans.show', $loan));

        Storage::disk('public')->assertExists($loan->departure_speedometer_photo_path);
        Storage::disk('public')->assertExists($loan->return_speedometer_photo_path);
    }

    public function test_user_can_submit_vehicle_loan_with_custom_choices(): void
    {
        $user = User::query()->first();

        $depPhoto = UploadedFile::fake()->image('speedo_dep2.jpg');
        $retPhoto = UploadedFile::fake()->image('speedo_ret2.jpg');

        $response = $this->actingAs($user)->post(route('vehicle-loans.store'), [
            'pic_name' => 'Budi Santoso',
            'driver_name' => 'Ahmad Driver',
            'departure_date' => '2026-11-04',
            'return_date' => '2026-11-05',
            'departure_km' => '88.100',
            'vehicle_type_choice' => 'other',
            'vehicle_type_custom' => 'Toyota Hilux 4x4',
            'purpose' => 'Survei medan terjal',
            'cleanliness_choice' => 'other',
            'cleanliness_custom' => 'Dicuci luar dalam sebelum kembali',
            'departure_speedometer_photo' => $depPhoto,
            'return_speedometer_photo' => $retPhoto,
        ]);

        $this->assertDatabaseHas('vehicle_loans', [
            'vehicle_type' => 'Toyota Hilux 4x4',
            'cleanliness_condition' => 'Dicuci luar dalam sebelum kembali',
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\EmployeeLeave;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmployeeLeaveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
    }

    public function test_user_can_view_leave_form(): void
    {
        $user = User::query()->first();

        $response = $this->actingAs($user)->get(route('leaves.create'));

        $response->assertOk();
        $response->assertSee('Formulir Izin/Cuti/WFA Karyawan & Magang');
        $response->assertSee('1. Nama');
        $response->assertSee('2. Jenis Izin yang Diambil');
        $response->assertSee('3. Jumlah Hari Izin / Cuti / WFA');
        $response->assertSee('4. Keterangan');
        $response->assertSee('5. Lampiran');
        $response->assertSee('Cuti');
        $response->assertSee('Tahunan');
        $response->assertSee('Izin sakit');
        $response->assertSee('Izin keperluan pribadi');
        $response->assertSee('Izin pelatihan / sertifikasi');
        $response->assertSee('WFA');
    }

    public function test_submitting_leave_form_validates_required_fields(): void
    {
        $user = User::query()->first();

        $response = $this->actingAs($user)->post(route('leaves.store'), []);

        $response->assertSessionHasErrors([
            'name',
            'leave_type',
            'days_count',
            'description',
        ]);
        $response->assertSessionDoesntHaveErrors(['attachment']);
    }

    public function test_user_can_submit_leave_form_without_attachment(): void
    {
        $user = User::query()->first();

        $response = $this->actingAs($user)->post(route('leaves.store'), [
            'name' => 'Bambang Sudirman',
            'leave_type' => 'WFA',
            'days_count' => 1,
            'description' => 'Bekerja dari rumah karena perbaikan listrik.',
        ]);

        $this->assertDatabaseHas('employee_leaves', [
            'name' => 'Bambang Sudirman',
            'leave_type' => 'WFA',
            'days_count' => 1,
            'attachment_path' => null,
        ]);
    }

    public function test_user_can_submit_leave_form_with_valid_data(): void
    {
        $user = User::query()->first();
        $attachment = UploadedFile::fake()->create('surat_dokter.pdf', 300, 'application/pdf');

        $response = $this->actingAs($user)->post(route('leaves.store'), [
            'name' => 'Bambang Sudirman',
            'leave_type' => 'Izin sakit',
            'days_count' => 2,
            'description' => 'Demam dan istirahat dokter selama 2 hari.',
            'attachment' => $attachment,
        ]);

        $this->assertDatabaseHas('employee_leaves', [
            'name' => 'Bambang Sudirman',
            'leave_type' => 'Izin sakit',
            'days_count' => 2,
            'attachment_name' => 'surat_dokter.pdf',
        ]);

        $leave = EmployeeLeave::query()->first();
        $response->assertRedirect(route('leaves.show', $leave));

        Storage::disk('public')->assertExists($leave->attachment_path);
    }

    public function test_user_can_download_leave_attachment(): void
    {
        $user = User::query()->first();
        $attachment = UploadedFile::fake()->create('undangan_pelatihan.pdf', 150, 'application/pdf');

        $this->actingAs($user)->post(route('leaves.store'), [
            'name' => 'Dewi Sartika',
            'leave_type' => 'Izin pelatihan / sertifikasi',
            'days_count' => 3,
            'description' => 'Mengikuti pelatihan ISO 9001 di Bandung.',
            'attachment' => $attachment,
        ]);

        $leave = EmployeeLeave::query()->first();

        $response = $this->actingAs($user)->get(route('leaves.download', $leave));
        $response->assertOk();
    }
}

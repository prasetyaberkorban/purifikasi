<?php

namespace Tests\Feature;

use App\Models\MeetingAttendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeetingAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_can_view_attendance_form(): void
    {
        $user = User::query()->first();

        $response = $this->actingAs($user)->get(route('attendances.create'));

        $response->assertOk();
        $response->assertSee('Formulir Daftar Hadir');
        $response->assertSee('Daftar Hadir peserta terkait rapat dan/atau diskusi di PT Purifikasi Andalan Diponegoro');
        $response->assertSee('1. Nama Lengkap');
        $response->assertSee('2. Jabatan');
        $response->assertSee('3. Cabang');
        $response->assertSee('Semarang 1');
        $response->assertSee('Semarang 2');
        $response->assertSee('Bandung');
        $response->assertSee('4. Jenis Kegiatan');
        $response->assertSee('Rapat Manajemen');
        $response->assertSee('Rapat Teknis');
        $response->assertSee('Rapat Keuangan');
        $response->assertSee('Rapat Pusat PT Purifikasi Andalan Diponegoro (SMG1, SMG2, Bandung)');
        $response->assertSee('Rapat K3LH');
        $response->assertSee('5. Tanggal Pelaksanaan');
        $response->assertSee('6. Pimpinan Rapat / Pemateri');
        $response->assertSee('7. Kegiatan / Materi yang Disampaikan');
    }

    public function test_submitting_attendance_form_validates_required_fields(): void
    {
        $user = User::query()->first();

        $response = $this->actingAs($user)->post(route('attendances.store'), []);

        $response->assertSessionHasErrors([
            'full_name',
            'position',
            'branch_choice',
            'activity_type_choice',
            'event_date',
            'meeting_leader',
            'material_summary',
        ]);
    }

    public function test_user_can_submit_attendance_form_with_valid_data(): void
    {
        $user = User::query()->first();

        $response = $this->actingAs($user)->post(route('attendances.store'), [
            'full_name' => 'Ahmad Fauzi',
            'position' => 'Supervisor Lapangan',
            'branch_choice' => 'Semarang 1',
            'activity_type_choice' => 'Rapat Teknis',
            'event_date' => '2026-11-04',
            'meeting_leader' => 'Ir. Bambang Tri',
            'material_summary' => 'Evaluasi hasil pemeliharaan unit filter air dan perencanaan pergantian membran.',
        ]);

        $this->assertDatabaseHas('meeting_attendances', [
            'full_name' => 'Ahmad Fauzi',
            'position' => 'Supervisor Lapangan',
            'branch' => 'Semarang 1',
            'activity_type' => 'Rapat Teknis',
            'meeting_leader' => 'Ir. Bambang Tri',
        ]);

        $attendance = MeetingAttendance::first();
        $this->assertNotNull($attendance);
        $response->assertRedirect(route('attendances.show', $attendance));
    }

    public function test_user_can_submit_attendance_with_custom_other_choices(): void
    {
        $user = User::query()->first();

        $response = $this->actingAs($user)->post(route('attendances.store'), [
            'full_name' => 'Siti Nurhaliza',
            'position' => 'Analis Laboratorium',
            'branch_choice' => 'other',
            'branch_custom' => 'Surabaya Project Office',
            'activity_type_choice' => 'other',
            'activity_type_custom' => 'Sosialisasi SOP Baru Uji Kualitas Air',
            'event_date' => '2026-11-04',
            'meeting_leader' => 'Dr. Hendra',
            'material_summary' => 'Pemaparan parameter baru dan standar uji mikrobiologi.',
        ]);

        $this->assertDatabaseHas('meeting_attendances', [
            'full_name' => 'Siti Nurhaliza',
            'branch' => 'Surabaya Project Office',
            'activity_type' => 'Sosialisasi SOP Baru Uji Kualitas Air',
        ]);
    }
}

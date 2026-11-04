<?php

namespace Tests\Feature;

use App\Models\FatigueAssessment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FatigueAssessmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_can_view_fatigue_assessment_form(): void
    {
        $user = User::query()->first();

        $response = $this->actingAs($user)->get(route('fatigue.create'));

        $response->assertOk();
        $response->assertSee('Penilaian Fatik Individual');
        $response->assertSee('1. Nama');
        $response->assertSee('2. Tanggal Penilaian Fatik Individual');
        $response->assertSee('3. Dalam 1 minggu terakhir apakah Anda mengalami sakit ringan');
        $response->assertSee('4. Apakah Anda mengkonsumsi obat atau zat');
        $response->assertSee('5. Apakah ada hal lain yang menggangu Anda bekerja saat ini?');
        $response->assertSee('6. Tadi malam Anda tidur berapa jam ?');
        $response->assertSee('7. Apakah Anda yakin/ mampu mengendarai kendaraan');
        $response->assertSee('Penilaian Fatik Individual adalah proses evaluasi');
    }

    public function test_submitting_fatigue_form_validates_required_fields(): void
    {
        $user = User::query()->first();

        $response = $this->actingAs($user)->post(route('fatigue.store'), []);

        $response->assertSessionHasErrors([
            'name',
            'assessment_date',
            'recent_illness',
            'medication_drowsy',
            'personal_distraction',
            'sleep_hours',
            'fit_to_drive',
        ]);
    }

    public function test_user_can_submit_fatigue_assessment(): void
    {
        $user = User::query()->first();

        $response = $this->actingAs($user)->post(route('fatigue.store'), [
            'name' => 'Hendro Prasetyo',
            'assessment_date' => now()->toDateString(),
            'recent_illness' => 'Tidak',
            'medication_drowsy' => 'Tidak',
            'personal_distraction' => 'Tidak',
            'sleep_hours' => 'lebih dari 6 jam',
            'fit_to_drive' => 'Ya',
        ]);

        $this->assertDatabaseHas('fatigue_assessments', [
            'name' => 'Hendro Prasetyo',
            'recent_illness' => 'Tidak',
            'sleep_hours' => 'lebih dari 6 jam',
            'fit_to_drive' => 'Ya',
        ]);

        $assessment = FatigueAssessment::query()->first();
        $this->assertTrue($assessment->isFullyFit());
        $response->assertRedirect(route('fatigue.show', $assessment));
    }
}

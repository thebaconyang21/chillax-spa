<?php

namespace Tests\Feature;

use App\Models\Therapist;
use App\Models\TherapistSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TherapistScheduleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_schedule_creation_rejects_overlaps_and_past_dates_but_allows_adjacent_times(): void
    {
        $this->travelTo('2026-11-27 08:00:00');
        $admin = User::factory()->create(['role' => 'admin']);
        $therapist = Therapist::create(['therapist_name' => 'Therapist A']);
        $otherTherapist = Therapist::create(['therapist_name' => 'Therapist B']);
        $scheduleDate = now()->addDay()->toDateString();
        $existingSchedule = TherapistSchedule::create([
            'therapist_id' => $therapist->id,
            'schedule_date' => $scheduleDate,
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
        ]);
        TherapistSchedule::create([
            'therapist_id' => $otherTherapist->id,
            'schedule_date' => $scheduleDate,
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
        ]);

        $this->actingAs($admin)->from(route('therapists'))->post(route('therapist-schedules.store'), [
            'therapist_id' => $therapist->id,
            'schedule_date' => $scheduleDate,
            'start_time' => '10:30',
            'end_time' => '11:30',
        ])->assertRedirect(route('therapists'))
            ->assertSessionHas('error', 'This overlaps an existing schedule for this therapist.');

        $this->assertDatabaseCount('therapist_schedules', 2);

        $this->actingAs($admin)->from(route('therapists'))->post(route('therapist-schedules.store'), [
            'therapist_id' => $therapist->id,
            'schedule_date' => now()->subDay()->toDateString(),
            'start_time' => '11:00',
            'end_time' => '12:00',
        ])->assertRedirect(route('therapists'))
            ->assertSessionHasErrors('schedule_date');

        $this->actingAs($admin)->from(route('therapists'))->post(route('therapist-schedules.store'), [
            'therapist_id' => $therapist->id,
            'schedule_date' => $scheduleDate,
            'start_time' => '12:00',
            'end_time' => '12:00',
        ])->assertRedirect(route('therapists'))
            ->assertSessionHasErrors('end_time');

        $this->actingAs($admin)->post(route('therapist-schedules.store'), [
            'therapist_id' => $therapist->id,
            'schedule_date' => $scheduleDate,
            'start_time' => '11:00',
            'end_time' => '12:00',
        ])->assertRedirect(route('therapists'))
            ->assertSessionHas('success', 'Schedule added successfully.');

        $this->assertDatabaseCount('therapist_schedules', 3);
        $this->assertModelExists($existingSchedule);
    }

    public function test_schedule_update_ignores_itself_rejects_other_overlaps_and_delete_removes_schedule(): void
    {
        $this->travelTo('2026-11-27 08:00:00');
        $admin = User::factory()->create(['role' => 'admin']);
        $therapist = Therapist::create(['therapist_name' => 'Therapist A']);
        $scheduleDate = now()->addDay()->toDateString();
        $schedule = TherapistSchedule::create([
            'therapist_id' => $therapist->id,
            'schedule_date' => $scheduleDate,
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
        ]);
        $otherSchedule = TherapistSchedule::create([
            'therapist_id' => $therapist->id,
            'schedule_date' => $scheduleDate,
            'start_time' => '12:00:00',
            'end_time' => '13:00:00',
        ]);

        $this->actingAs($admin)->put(route('therapist-schedules.update', $schedule), [
            'therapist_id' => $therapist->id,
            'schedule_date' => $scheduleDate,
            'start_time' => '11:00',
            'end_time' => '12:00',
        ])->assertRedirect(route('therapists'))
            ->assertSessionHas('success', 'Schedule updated successfully.');

        $this->assertDatabaseHas('therapist_schedules', [
            'id' => $schedule->id,
            'start_time' => '11:00:00',
            'end_time' => '12:00:00',
        ]);

        $this->actingAs($admin)->from(route('therapists'))->put(route('therapist-schedules.update', $schedule), [
            'therapist_id' => $therapist->id,
            'schedule_date' => $scheduleDate,
            'start_time' => '12:30',
            'end_time' => '13:30',
        ])->assertRedirect(route('therapists'))
            ->assertSessionHas('error', 'This overlaps an existing schedule for this therapist.');

        $this->assertDatabaseHas('therapist_schedules', [
            'id' => $schedule->id,
            'start_time' => '11:00:00',
            'end_time' => '12:00:00',
        ]);

        $this->actingAs($admin)->delete(route('therapist-schedules.destroy', $otherSchedule))
            ->assertRedirect(route('therapists'))
            ->assertSessionHas('success', 'Schedule deleted successfully.');

        $this->assertDatabaseMissing('therapist_schedules', ['id' => $otherSchedule->id]);
    }
}

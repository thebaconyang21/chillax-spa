<?php

namespace App\Http\Controllers;

use App\Models\Therapist;
use App\Models\TherapistSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TherapistScheduleController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'therapist_id' => ['required', 'integer', 'exists:therapists,id'],
            'schedule_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);
        $validated['start_time'] .= ':00';
        $validated['end_time'] .= ':00';

        $created = DB::transaction(function () use ($validated): bool {
            Therapist::query()
                ->whereKey($validated['therapist_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($this->overlaps($validated)) {
                return false;
            }

            TherapistSchedule::create($validated);

            return true;
        });

        if (! $created) {
            return back()->withInput()->with('error', 'This overlaps an existing schedule for this therapist.');
        }

        return redirect()->route('therapists')->with('success', 'Schedule added successfully.');
    }

    public function update(Request $request, TherapistSchedule $schedule): RedirectResponse
    {
        $validated = $request->validate([
            'therapist_id' => ['required', 'integer', 'exists:therapists,id'],
            'schedule_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);
        $validated['start_time'] .= ':00';
        $validated['end_time'] .= ':00';

        $updated = DB::transaction(function () use ($validated, $schedule): bool {
            $lockedSchedule = TherapistSchedule::query()
                ->lockForUpdate()
                ->findOrFail($schedule->getKey());
            Therapist::query()
                ->whereKey($validated['therapist_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($this->overlaps($validated, $lockedSchedule)) {
                return false;
            }

            $lockedSchedule->update($validated);

            return true;
        });

        if (! $updated) {
            return back()->withInput()->with('error', 'This overlaps an existing schedule for this therapist.');
        }

        return redirect()->route('therapists')->with('success', 'Schedule updated successfully.');
    }

    public function destroy(TherapistSchedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return redirect()->route('therapists')->with('success', 'Schedule deleted successfully.');
    }

    /**
     * @param  array{therapist_id: int|string, schedule_date: string, start_time: string, end_time: string}  $validated
     */
    private function overlaps(array $validated, ?TherapistSchedule $schedule = null): bool
    {
        return TherapistSchedule::query()
            ->where('therapist_id', $validated['therapist_id'])
            ->whereDate('schedule_date', $validated['schedule_date'])
            ->where('start_time', '<', $validated['end_time'])
            ->where('end_time', '>', $validated['start_time'])
            ->when($schedule, fn (Builder $query): Builder => $query->where('id', '!=', $schedule->getKey()))
            ->exists();
    }
}

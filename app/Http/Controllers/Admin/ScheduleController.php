<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedules = Schedule::orderBy('date', 'desc')
                            ->orderBy('start_time', 'asc')
                            ->paginate(10);

        return Inertia::render('Admin/Schedules/ScheduleIndex', [
            'schedules' => $schedules,
            'flash' => ['success' => session('success')]
        ]);
    }

    /**
     * Get all schedules data for API
     */
    public function getListeDatas()
    {
        $schedules = Schedule::orderBy('date', 'desc')
                            ->orderBy('start_time', 'asc')
                            ->get();

        return response()->json($schedules);
    }

    /**
     * Get available schedules for public display
     */
    public function getAvailableSchedules()
    {
        $schedules = Schedule::where('is_active', true)
                            ->where('date', '>=', now()->format('Y-m-d'))
                            ->orderBy('date', 'asc')
                            ->orderBy('start_time', 'asc')
                            ->get();

        return response()->json($schedules);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slots' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Schedule::create($data);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Créneau de disponibilité créé.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slots' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $schedule->update($data);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Créneau de disponibilité mis à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Créneau de disponibilité supprimé.');
    }

    /**
     * Duplicate a schedule for multiple weeks.
     */
    public function duplicate(Request $request, Schedule $schedule)
    {
        $data = $request->validate([
            'weeks' => 'required|integer|min:1|max:52',
        ]);

        $weeks = $data['weeks'];
        $originalDate = new \DateTime($schedule->date);
        $successCount = 0;

        for ($i = 1; $i <= $weeks; $i++) {
            // Clone the date and add weeks
            $newDate = clone $originalDate;
            $newDate->modify("+{$i} week");

            // Create a new schedule with the same data but different date
            Schedule::create([
                'date' => $newDate->format('Y-m-d'),
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'slots' => $schedule->slots,
                'description' => $schedule->description,
                'is_active' => $schedule->is_active,
            ]);

            $successCount++;
        }

        return redirect()->route('admin.schedules.index')
            ->with('success', "{$successCount} créneaux ont été dupliqués avec succès.");
    }
}

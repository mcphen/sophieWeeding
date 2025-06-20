<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Schedule;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the appointments in admin panel.
     */
    public function index()
    {
        // Get all appointments with their relationships
        $appointments = Appointment::with(['client', 'schedule', 'services'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Admin/Appointments/AppointmentIndex', [
            'appointments' => $appointments,
        ]);
    }

    /**
     * Display the specified appointment in admin panel.
     */
    public function show(Appointment $appointment)
    {
        // Load the appointment with its relationships
        $appointment->load(['client', 'schedule', 'services']);

        return Inertia::render('Admin/Appointments/AppointmentShow', [
            'appointment' => $appointment,
        ]);
    }

    /**
     * Update the specified appointment in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        // Validate the request
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
            'description' => 'nullable|string|max:1000',
        ]);

        // Update the appointment
        $appointment->update($validated);

        return redirect()->back()->with('success', 'Rendez-vous mis à jour avec succès.');
    }

    /**
     * Remove the specified appointment from storage.
     */
    public function destroy(Appointment $appointment)
    {
        // Delete the appointment
        $appointment->delete();

        return redirect()->route('admin.appointments.index')->with('success', 'Rendez-vous supprimé avec succès.');
    }

    /**
     * Confirm the specified appointment.
     */
    public function confirm(Request $request, Appointment $appointment)
    {
        // Update the appointment status to confirmed
        $appointment->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'confirmed_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Rendez-vous confirmé avec succès.');
    }

    /**
     * Display the appointment booking form.
     */
    public function create()
    {
        // Get all active services
        $services = Service::all();

        // Get all active schedules with available slots
        $schedules = Schedule::where('is_active', true)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->whereRaw('(SELECT COUNT(*) FROM appointments WHERE appointments.schedule_id = schedules.id) < schedules.slots')
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return Inertia::render('Front/Appointment/Create', [
            'services' => $services,
            'schedules' => $schedules,
        ]);
    }

    /**
     * Store a newly created appointment in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'schedule_id' => 'required|exists:schedules,id',
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        // Check if the schedule is still available
        $schedule = Schedule::findOrFail($validated['schedule_id']);
        $appointmentCount = Appointment::where('schedule_id', $schedule->id)->count();

        if ($appointmentCount >= $schedule->slots) {
            return back()->withErrors([
                'schedule_id' => 'Ce créneau n\'est plus disponible. Veuillez en choisir un autre.',
            ]);
        }

        // Start a database transaction
        return DB::transaction(function () use ($validated) {
            // Create or find the client
            $client = Client::firstOrCreate(
                ['email' => $validated['email']],
                [
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'phone' => $validated['phone'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'is_lead' => true,
                ]
            );

            // Create the appointment
            $appointment = Appointment::create([
                'client_id' => $client->id,
                'schedule_id' => $validated['schedule_id'],
                'subject' => $validated['subject'],
                'description' => $validated['description'] ?? null,
                'status' => 'pending',
            ]);

            // Attach services to the appointment
            $appointment->services()->attach($validated['service_ids']);

            return redirect()->route('appointment.confirmation', $appointment->id)
                ->with('success', 'Votre rendez-vous a été enregistré avec succès.');
        });
    }

    /**
     * Display the appointment confirmation page.
     */
    public function confirmation(Appointment $appointment)
    {
        // Load the appointment with its relationships
        $appointment->load(['client', 'schedule', 'services']);

        return Inertia::render('Front/Appointment/Confirmation', [
            'appointment' => $appointment,
        ]);
    }
}

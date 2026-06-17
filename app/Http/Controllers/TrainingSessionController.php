<?php

namespace App\Http\Controllers;

use App\Exports\SessionRegistrationsExport;
use App\Mail\MasterclassReminderMail;
use App\Models\Masterclass;
use App\Models\TrainingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class TrainingSessionController extends Controller
{
    public function create(Masterclass $masterclass)
    {
        return Inertia::render('Admin/MasterclassSessions/Create', [
            'masterclass' => [
                'id'    => $masterclass->id,
                'title' => $masterclass->title,
                'slug'  => $masterclass->slug,
            ],
        ]);
    }

    public function store(Request $request, Masterclass $masterclass)
    {
        $validated = $request->validate([
            'start_date'      => 'required|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'location_type'   => 'required|in:presentiel,online,both',
            'adresse'         => 'nullable|string|max:500',
            'google_maps_url' => 'nullable|url|max:1000',
            'online_link'     => 'nullable|url|max:1000',
            'price'           => 'nullable|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'is_active'       => 'boolean',
        ]);

        $session = $masterclass->sessions()->create([
            'start_date'       => $validated['start_date'],
            'end_date'         => $validated['end_date'] ?? null,
            'location_type'    => $validated['location_type'],
            'adresse'          => $validated['adresse'] ?? null,
            'google_maps_url'  => $validated['google_maps_url'] ?? null,
            'online_link'      => $validated['online_link'] ?? null,
            'price'            => $validated['price'] ?? null,
            'max_participants' => $validated['max_participants'] ?? null,
            'is_active'        => $validated['is_active'] ?? true,
        ]);

        // Notifie les personnes en liste d'attente pour cette masterclass
        \App\Models\WaitlistEntry::notifyAllForMasterclass($masterclass, $session);

        return redirect()->route('admin.masterclasses.show', $masterclass)
            ->with('success', 'Session ajoutée avec succès.');
    }

    public function edit(Masterclass $masterclass, TrainingSession $session)
    {
        return Inertia::render('Admin/MasterclassSessions/Edit', [
            'masterclass' => [
                'id'    => $masterclass->id,
                'title' => $masterclass->title,
                'slug'  => $masterclass->slug,
            ],
            'session' => [
                'id'               => $session->id,
                'start_date'       => $session->start_date->format('Y-m-d\TH:i'),
                'end_date'         => $session->end_date ? $session->end_date->format('Y-m-d\TH:i') : null,
                'location_type'    => $session->location_type,
                'adresse'          => $session->adresse,
                'google_maps_url'  => $session->google_maps_url,
                'online_link'      => $session->online_link,
                'replay_url'       => $session->replay_url,
                'price'            => $session->price,
                'max_participants' => $session->max_participants,
                'is_active'        => $session->is_active,
            ],
        ]);
    }

    public function update(Request $request, Masterclass $masterclass, TrainingSession $session)
    {
        $validated = $request->validate([
            'start_date'       => 'required|date',
            'end_date'         => 'nullable|date|after_or_equal:start_date',
            'location_type'    => 'required|in:presentiel,online,both',
            'adresse'          => 'nullable|string|max:500',
            'google_maps_url'  => 'nullable|url|max:1000',
            'online_link'      => 'nullable|url|max:1000',
            'replay_url'       => 'nullable|url|max:1000',
            'price'            => 'nullable|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'is_active'        => 'boolean',
        ]);

        $session->update([
            'start_date'       => $validated['start_date'],
            'end_date'         => $validated['end_date'] ?? null,
            'location_type'    => $validated['location_type'],
            'adresse'          => $validated['adresse'] ?? null,
            'google_maps_url'  => $validated['google_maps_url'] ?? null,
            'online_link'      => $validated['online_link'] ?? null,
            'replay_url'       => $validated['replay_url'] ?? null,
            'price'            => $validated['price'] ?? null,
            'max_participants' => $validated['max_participants'] ?? null,
            'is_active'        => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.masterclasses.show', $masterclass)
            ->with('success', 'Session mise à jour avec succès.');
    }

    public function destroy(Masterclass $masterclass, TrainingSession $session)
    {
        $session->delete();

        return redirect()->route('admin.masterclasses.show', $masterclass)
            ->with('success', 'Session supprimée avec succès.');
    }

    public function showRegistrations(Masterclass $masterclass, TrainingSession $session)
    {
        $registrations = $session->registrations()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($r) => [
                'id'           => $r->id,
                'name'         => $r->name,
                'email'        => $r->email,
                'phone'        => $r->phone,
                'message'      => $r->message,
                'is_confirmed' => $r->is_confirmed,
                'confirmed_at' => $r->confirmed_at?->format('d/m/Y H:i'),
                'created_at'   => $r->created_at->format('d/m/Y H:i'),
            ]);

        $waitlistCount = \App\Models\WaitlistEntry::where('training_session_id', $session->id)
            ->whereNull('notified_at')
            ->count();

        return Inertia::render('Admin/MasterclassSessions/Registrations', [
            'masterclass' => [
                'id'    => $masterclass->id,
                'title' => $masterclass->title,
            ],
            'session' => [
                'id'                 => $session->id,
                'start_date'         => $session->start_date->format('d/m/Y H:i'),
                'location_label'     => $session->location_label,
                'registration_count' => $session->registration_count,
                'max_participants'   => $session->max_participants,
            ],
            'registrations'  => $registrations,
            'waitlist_count' => $waitlistCount,
        ]);
    }

    public function exportRegistrations(Masterclass $masterclass, TrainingSession $session)
    {
        $filename = 'inscriptions-' . $masterclass->slug . '-' . $session->start_date->format('Y-m-d') . '.xlsx';
        return Excel::download(new SessionRegistrationsExport($session), $filename);
    }

    public function sendReminder(Masterclass $masterclass, TrainingSession $session)
    {
        $registrations = $session->registrations()->get();

        if ($registrations->isEmpty()) {
            return redirect()->back()->with('error', 'Aucun inscrit pour cette session.');
        }

        $sent = 0;
        foreach ($registrations as $registration) {
            try {
                Mail::to($registration->email)->send(new MasterclassReminderMail($registration, $masterclass, $session));
                $sent++;
            } catch (\Exception $e) {
                // continue pour les autres
            }
        }

        return redirect()->back()->with('success', "Email de rappel envoyé à {$sent} inscrit(s).");
    }
}

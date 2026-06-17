<?php

namespace App\Http\Controllers;

use App\Mail\AttestationAvailableMail;
use App\Mail\MasterclassRegistrationAdminMail;
use App\Mail\MasterclassRegistrationConfirmationMail;
use App\Models\Masterclass;
use App\Models\TrainingRegistration;
use App\Models\TrainingSession;
use App\Services\ContactService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class TrainingRegistrationController extends Controller
{
    public function store(Request $request, Masterclass $masterclass, TrainingSession $session)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'required|string|max:20',
            'message' => 'nullable|string',
        ]);

        if ($session->masterclass_id !== $masterclass->id) {
            abort(404);
        }

        if ($session->isFull()) {
            return redirect()->back()
                ->with('error', 'Désolé, cette session est complète.');
        }

        $registration = $session->registrations()->create([
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'phone'   => $validated['phone'] ?? null,
            'message' => $validated['message'] ?? null,
        ]);

        // Email au client (administrateur)
        $adminEmail = app(ContactService::class)->getContactSettings()['email'] ?? config('mail.from.address');
        try {
            Mail::to($adminEmail)->send(new MasterclassRegistrationAdminMail($registration, $masterclass, $session));
        } catch (\Exception $e) {
            // Ne pas bloquer l'inscription si l'email échoue
        }

        // Email de confirmation au prospect
        try {
            Mail::to($validated['email'])->send(new MasterclassRegistrationConfirmationMail($registration, $masterclass, $session));
        } catch (\Exception $e) {
            //
        }

        return redirect()->route('masterclass.registration.confirmation', $registration->id)
            ->with('success', 'Votre inscription a été enregistrée avec succès.');
    }

    public function confirmation(TrainingRegistration $registration)
    {
        $registration->load('trainingSession.masterclass');
        $session = $registration->trainingSession;
        $masterclass = $session->masterclass;

        return Inertia::render('Front/MasterclassRegistrationConfirmation', [
            'registration' => [
                'id'         => $registration->id,
                'name'       => $registration->name,
                'email'      => $registration->email,
                'created_at' => $registration->created_at->format('d/m/Y H:i'),
            ],
            'masterclass' => [
                'title'  => $masterclass->title,
                'niveau' => $masterclass->niveau,
            ],
            'session' => [
                'start_date'     => $session->start_date->format('d/m/Y'),
                'start_time'     => $session->start_date->format('H:i'),
                'location_label' => $session->location_label,
                'adresse'        => $session->adresse,
            ],
        ]);
    }

    public function adminStore(Request $request, Masterclass $masterclass, TrainingSession $session)
    {
        if ($session->masterclass_id !== $masterclass->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|max:255',
            'phone'            => 'nullable|string|max:20',
            'message'          => 'nullable|string',
            'send_confirmation' => 'boolean',
        ]);

        $registration = $session->registrations()->create([
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'phone'   => $validated['phone'] ?? null,
            'message' => $validated['message'] ?? null,
        ]);

        if (!empty($validated['send_confirmation'])) {
            try {
                Mail::to($validated['email'])->send(new MasterclassRegistrationConfirmationMail($registration, $masterclass, $session));
            } catch (\Exception $e) {
                //
            }
        }

        return redirect()->back()->with('success', 'Inscrit ajouté avec succès.');
    }

    public function confirm(TrainingRegistration $registration)
    {
        $registration->confirm();
        $registration->load('trainingSession.masterclass');

        try {
            Mail::to($registration->email)->send(new AttestationAvailableMail(
                $registration,
                $registration->trainingSession->masterclass,
                $registration->trainingSession
            ));
        } catch (\Exception $e) {
            //
        }

        return redirect()->back()
            ->with('success', 'Inscription confirmée — email envoyé au participant.');
    }

    public function confirmAll(Masterclass $masterclass, TrainingSession $session)
    {
        if ($session->masterclass_id !== $masterclass->id) {
            abort(404);
        }

        $registrations = $session->registrations()->where('is_confirmed', false)->get();

        if ($registrations->isEmpty()) {
            return redirect()->back()->with('info', 'Toutes les inscriptions sont déjà confirmées.');
        }

        $confirmed = 0;
        foreach ($registrations as $registration) {
            $registration->confirm();
            try {
                Mail::to($registration->email)->send(new AttestationAvailableMail($registration, $masterclass, $session));
            } catch (\Exception $e) {
                //
            }
            $confirmed++;
        }

        return redirect()->back()
            ->with('success', "{$confirmed} inscription(s) confirmée(s) — emails envoyés aux participants.");
    }

    public function adminAttestation(TrainingRegistration $registration)
    {
        $registration->load('trainingSession.masterclass');
        $session = $registration->trainingSession;
        $masterclass = $session->masterclass;

        $pdf = Pdf::loadView('pdf.attestation', compact('registration', 'session', 'masterclass'));
        $pdf->setPaper('A3', 'landscape');

        $filename = 'attestation-' . str_pad($registration->id, 5, '0', STR_PAD_LEFT) . '-' . \Str::slug($registration->name) . '.pdf';

        return $pdf->download($filename);
    }

    public function destroy(TrainingRegistration $registration)
    {
        $session = $registration->trainingSession;
        $registration->delete();

        // Si la session avait une liste d'attente, notifie la prochaine personne
        if ($session) {
            \App\Models\WaitlistEntry::notifyNextForSession($session);
        }

        return redirect()->back()
            ->with('success', 'Inscription supprimée avec succès.');
    }
}

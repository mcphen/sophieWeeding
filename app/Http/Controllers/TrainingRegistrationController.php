<?php

namespace App\Http\Controllers;

use App\Models\TrainingRegistration;
use App\Models\TrainingSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrainingRegistrationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, TrainingSession $trainingSession)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'nullable|string',
        ]);

        // Check if the training session is full
        if ($trainingSession->isFull()) {
            return redirect()->back()
                ->with('error', 'Désolé, cette session de formation est complète.');
        }

        // Create registration
        $registration = $trainingSession->registrations()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'message' => $validated['message'] ?? null,
        ]);

        return redirect()->route('training.registration.confirmation', $registration->id)
            ->with('success', 'Votre inscription a été enregistrée avec succès.');
    }

    /**
     * Display the registration confirmation page.
     */
    public function confirmation(TrainingRegistration $registration)
    {
        $trainingSession = $registration->trainingSession;

        return Inertia::render('Front/TrainingRegistrationConfirmation', [
            'registration' => [
                'id' => $registration->id,
                'name' => $registration->name,
                'email' => $registration->email,
                'phone' => $registration->phone,
                'created_at' => $registration->created_at->format('d/m/Y H:i'),
            ],
            'trainingSession' => [
                'id' => $trainingSession->id,
                'title' => $trainingSession->title,
                'start_date' => $trainingSession->start_date->format('d/m/Y H:i'),
                'end_date' => $trainingSession->end_date->format('d/m/Y H:i'),
                'location' => $trainingSession->location,
            ],
        ]);
    }

    /**
     * Confirm a registration.
     */
    public function confirm(TrainingRegistration $registration)
    {
        $registration->confirm();

        return redirect()->back()
            ->with('success', 'Inscription confirmée avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrainingRegistration $registration)
    {
        $registration->delete();

        return redirect()->back()
            ->with('success', 'Inscription supprimée avec succès.');
    }
}

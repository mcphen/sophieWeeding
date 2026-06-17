<?php

namespace App\Http\Controllers;

use App\Mail\WaitlistConfirmationMail;
use App\Models\Masterclass;
use App\Models\TrainingSession;
use App\Models\WaitlistEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WaitlistController extends Controller
{
    public function store(Request $request, Masterclass $masterclass, TrainingSession $session)
    {
        if ($session->masterclass_id !== $masterclass->id) {
            abort(404);
        }

        if (!$session->isFull()) {
            return redirect()->route('masterclass.show', $masterclass->slug)
                ->with('info', 'Des places sont disponibles, inscrivez-vous directement.');
        }

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        // Évite les doublons sur la même session
        $exists = WaitlistEntry::where('training_session_id', $session->id)
            ->where('email', $validated['email'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('waitlist_info', 'Votre adresse email est déjà enregistrée sur la liste d\'attente pour cette session.');
        }

        $entry = WaitlistEntry::create([
            'training_session_id' => $session->id,
            'masterclass_id'      => $masterclass->id,
            'name'                => $validated['name'],
            'email'               => $validated['email'],
            'phone'               => $validated['phone'] ?? null,
        ]);

        try {
            Mail::to($entry->email)->send(new WaitlistConfirmationMail($entry, $masterclass, $session));
        } catch (\Exception) {
            // Silent fail
        }

        return redirect()->back()
            ->with('waitlist_success', 'Vous avez été ajouté(e) à la liste d\'attente. Nous vous contacterons dès qu\'une place se libère.');
    }
}

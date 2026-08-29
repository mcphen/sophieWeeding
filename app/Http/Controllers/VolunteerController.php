<?php

namespace App\Http\Controllers;

use App\Models\Volunteer;
use App\Services\ContactService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class VolunteerController extends Controller
{
    protected ContactService $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function create()
    {
        return Inertia::render('Front/Volunteer', [
            'contactSettings' => $this->contactService->getContactSettings(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:30',
            'type' => 'required|string|in:benevolat,partenariat',
            'motivation' => 'required|string|max:1000',
        ]);

        $volunteer = Volunteer::create($validated);

        try {
            $adminEmail = $this->contactService->getContactSettings()['contact_email'];
            Mail::raw(
                "Nouvelle candidature\n\nNom : {$volunteer->name}\nTéléphone : {$volunteer->phone}\nEmail : {$volunteer->email}\nType : {$volunteer->type}\nMotivation : {$volunteer->motivation}",
                function ($mail) use ($adminEmail, $volunteer) {
                    $mail->to($adminEmail)
                        ->subject('Nouvelle candidature - ' . ($volunteer->type === 'benevolat' ? 'Bénévolat' : 'Partenariat'));
                }
            );
        } catch (\Exception $e) {
            Log::error('Failed to send volunteer notification email', [
                'error' => $e->getMessage(),
                'volunteer_id' => $volunteer->id,
            ]);
        }

        return back()->with('success', 'Merci pour votre engagement ! Notre équipe reviendra vers vous rapidement.');
    }

    public function adminIndex()
    {
        $volunteers = Volunteer::orderBy('created_at', 'desc')->paginate(15);

        return Inertia::render('Admin/Volunteers/Index', [
            'volunteers' => $volunteers,
        ]);
    }

    public function destroy(Volunteer $volunteer)
    {
        $volunteer->delete();

        return redirect()->route('admin.volunteers.index')->with('success', 'Candidature supprimée avec succès.');
    }
}

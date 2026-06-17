<?php

namespace App\Http\Controllers;

use App\Mail\ProspectMagicLinkMail;
use App\Models\ProspectToken;
use App\Models\TrainingRegistration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class ProspectPortalController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Front/ProspectPortal/Login');
    }

    public function sendLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = strtolower(trim($request->email));

        $hasRegistrations = TrainingRegistration::where('email', $email)->exists();

        if ($hasRegistrations) {
            $token = ProspectToken::createForEmail($email);
            $loginUrl = route('prospect.portal.verify', $token->token);

            try {
                Mail::to($email)->send(new ProspectMagicLinkMail($loginUrl));
            } catch (\Exception $e) {
                // Ne pas révéler l'erreur
            }
        }

        return redirect()->back()->with('success', 'Si votre email est associé à une inscription, vous recevrez un lien de connexion dans quelques instants.');
    }

    public function verify(string $token)
    {
        $record = ProspectToken::where('token', $token)->first();

        if (!$record || !$record->isValid()) {
            return redirect()->route('prospect.portal.login')
                ->with('error', 'Ce lien est invalide ou a expiré. Veuillez en demander un nouveau.');
        }

        $record->markUsed();

        session(['prospect_email' => $record->email]);

        return redirect()->route('prospect.portal.dashboard');
    }

    public function dashboard(Request $request)
    {
        $email = $request->session()->get('prospect_email');

        $registrations = TrainingRegistration::where('email', $email)
            ->with('trainingSession.masterclass')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($r) {
                $session = $r->trainingSession;
                $masterclass = $session?->masterclass;
                $isPast = $session && $session->start_date->isPast();

                return [
                    'id'           => $r->id,
                    'name'         => $r->name,
                    'email'        => $r->email,
                    'is_confirmed' => $r->is_confirmed,
                    'created_at'   => $r->created_at->format('d/m/Y'),
                    'masterclass'  => $masterclass ? [
                        'title'  => $masterclass->title,
                        'niveau' => $masterclass->niveau,
                        'slug'   => $masterclass->slug,
                    ] : null,
                    'session' => $session ? [
                        'id'             => $session->id,
                        'start_date'     => $session->start_date->format('d/m/Y'),
                        'start_time'     => $session->start_date->format('H:i'),
                        'location_label' => $session->location_label,
                        'formatted_price' => $session->formatted_price,
                        'is_past'        => $isPast,
                        'replay_url'     => ($isPast && $session->replay_url) ? $session->replay_url : null,
                    ] : null,
                ];
            });

        $signaturePath = \App\Models\Setting::get('attestation_signature');
        $hasSignature = $signaturePath && \Illuminate\Support\Facades\Storage::disk('public')->exists($signaturePath);

        return Inertia::render('Front/ProspectPortal/Dashboard', [
            'email'         => $email,
            'registrations' => $registrations,
            'has_signature' => $hasSignature,
        ]);
    }

    public function attestation(Request $request, TrainingRegistration $registration)
    {
        $email = $request->session()->get('prospect_email');

        if (strtolower($registration->email) !== strtolower($email)) {
            abort(403);
        }

        if (!$registration->is_confirmed) {
            return redirect()->route('prospect.portal.dashboard')
                ->with('error', 'L\'attestation n\'est disponible que pour les inscriptions confirmées.');
        }

        $signaturePath = \App\Models\Setting::get('attestation_signature');
        if (!$signaturePath || !\Illuminate\Support\Facades\Storage::disk('public')->exists($signaturePath)) {
            return redirect()->route('prospect.portal.dashboard')
                ->with('info', 'Votre attestation vous sera envoyée par email dès qu\'elle sera signée par notre équipe.');
        }

        $registration->load('trainingSession.masterclass');
        $session = $registration->trainingSession;
        $masterclass = $session->masterclass;

        $pdf = Pdf::loadView('pdf.attestation', compact('registration', 'session', 'masterclass'));
        $pdf->setPaper('A3', 'landscape');

        $filename = 'attestation-' . \Str::slug($masterclass->title) . '-' . $session->start_date->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    public function logout(Request $request)
    {
        $request->session()->forget('prospect_email');

        return redirect()->route('prospect.portal.login')
            ->with('success', 'Vous avez été déconnecté.');
    }
}

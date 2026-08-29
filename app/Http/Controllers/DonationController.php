<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Services\ContactService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class DonationController extends Controller
{
    protected ContactService $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function create()
    {
        return Inertia::render('Front/Donate', [
            'contactSettings' => $this->contactService->getContactSettings(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:30',
            'amount' => 'required|integer|min:500',
            'method' => 'required|string|in:orange_money,wave,virement',
            'message' => 'nullable|string|max:1000',
        ]);

        $donation = Donation::create($validated);

        try {
            $adminEmail = $this->contactService->getContactSettings()['contact_email'];
            Mail::raw(
                "Nouvelle intention de don\n\nNom : {$donation->name}\nTéléphone : {$donation->phone}\nEmail : {$donation->email}\nMontant : {$donation->amount} FCFA\nMoyen : {$donation->method}\nMessage : {$donation->message}",
                function ($mail) use ($adminEmail, $donation) {
                    $mail->to($adminEmail)
                        ->subject('Nouvelle intention de don - ' . $donation->amount . ' FCFA');
                }
            );
        } catch (\Exception $e) {
            Log::error('Failed to send donation notification email', [
                'error' => $e->getMessage(),
                'donation_id' => $donation->id,
            ]);
        }

        return back()->with('success', 'Merci pour votre générosité ! Notre équipe vous contactera pour finaliser votre don.');
    }

    public function adminIndex()
    {
        $donations = Donation::orderBy('created_at', 'desc')->paginate(15);

        return Inertia::render('Admin/Donations/Index', [
            'donations' => $donations,
        ]);
    }

    public function destroy(Donation $donation)
    {
        $donation->delete();

        return redirect()->route('admin.donations.index')->with('success', 'Don supprimé avec succès.');
    }
}

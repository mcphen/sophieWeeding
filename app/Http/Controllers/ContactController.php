<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ContactController extends Controller
{
    /**
     * Display a listing of the contacts in admin panel.
     */
    public function adminIndex()
    {
        // Get all contacts with their relationships
        $contacts = Contact::with(['client'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Admin/Contacts/ContactIndex', [
            'contacts' => $contacts,
        ]);
    }

    /**
     * Display the specified contact in admin panel.
     */
    public function show(Contact $contact)
    {
        // Load the contact with its relationships
        $contact->load(['client']);

        return Inertia::render('Admin/Contacts/ContactShow', [
            'contact' => $contact,
        ]);
    }

    /**
     * Remove the specified contact from storage.
     */
    public function destroy(Contact $contact)
    {
        // Delete the contact
        $contact->delete();

        return redirect()->route('admin.contacts.index')->with('success', 'Contact supprimé avec succès.');
    }

    /**
     * Get contact settings for front-end pages
     *
     * @return array
     */
    private function getContactSettings()
    {
        return [
            'contact_phone' => Setting::get('contact_phone', '(+221) 78 538 30 69'),
            'contact_phone_fixed' => Setting::get('contact_phone_fixed', '(+221) 33 865 27 11'),
            'contact_email' => Setting::get('contact_email', 'sophieweddings5@gmail.com'),
            'contact_address' => Setting::get('contact_address', 'Rue NG-70, 91 Ngor Almadies, Dakar 12000'),
            'social_facebook' => Setting::get('social_facebook', 'https://www.facebook.com/Sophieweddingsdreams22/'),
            'social_twitter' => Setting::get('social_twitter', '#'),
            'social_instagram' => Setting::get('social_instagram', 'https://www.instagram.com/sophie_weddings_dreams/'),
            'opening_hours' => Setting::get('opening_hours', 'Lundi - Vendredi: 8am - 6pm'),
        ];
    }

    /**
     * Display the contact page.
     */
    public function index()
    {
        return Inertia::render('Front/Contact', [
            'contactSettings' => $this->getContactSettings()
        ]);
    }

    /**
     * Store a new contact message.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        // Start a database transaction
        return DB::transaction(function () use ($validated) {
            // Create or find the client
            $client = Client::firstOrCreate(
                ['email' => $validated['email']],
                [
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'phone' => $validated['phone'] ?? null,
                    'is_lead' => true,
                ]
            );

            // Create the contact message
            Contact::create([
                'client_id' => $client->id,
                'subject' => $validated['subject'],
                'description' => $validated['description'],
            ]);

            return back()->with('success', 'Votre message a été envoyé avec succès.');
        });
    }
}

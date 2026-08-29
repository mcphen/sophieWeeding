<?php

namespace App\Http\Controllers;

use App\Helpers\StorageHelper;
use App\Models\Actualite;
use App\Models\Album;
use App\Models\Appointment;
use App\Models\Contact;
use App\Models\Donation;
use App\Models\EmailListEntry;
use App\Models\Masterclass;
use App\Models\Newsletter;
use App\Models\Setting;
use App\Models\TrainingRegistration;
use App\Models\Volunteer;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the business overview dashboard (KPIs across donations,
     * volunteers, appointments, events and content).
     */
    public function index()
    {
        $startOfMonth = Carbon::now()->startOfMonth();

        $kpis = [
            'donationsTotal' => (int) Donation::sum('amount'),
            'donationsCount' => Donation::count(),
            'donationsThisMonth' => (int) Donation::where('created_at', '>=', $startOfMonth)->sum('amount'),
            'volunteersCount' => Volunteer::count(),
            'volunteersThisMonth' => Volunteer::where('created_at', '>=', $startOfMonth)->count(),
            'contactsCount' => Contact::count(),
            'contactsThisMonth' => Contact::where('created_at', '>=', $startOfMonth)->count(),
            'appointmentsUpcoming' => Appointment::whereIn('status', ['pending', 'confirmed'])
                ->whereHas('schedule', fn ($q) => $q->where('date', '>=', Carbon::today()))
                ->count(),
            'appointmentsPending' => Appointment::where('status', 'pending')->count(),
            'registrationsTotal' => TrainingRegistration::count(),
            'registrationsConfirmed' => TrainingRegistration::confirmed()->count(),
            'newsletterSubscribers' => Newsletter::where('is_active', true)->count(),
            'emailListContacts' => EmailListEntry::count(),
        ];

        // Dons des 6 derniers mois
        $months = collect(range(5, 0))->map(fn ($i) => Carbon::now()->startOfMonth()->subMonths($i));
        $donationsMonthly = [
            'labels' => $months->map(fn ($m) => ucfirst($m->locale('fr')->translatedFormat('M Y')))->toArray(),
            'data' => $months->map(
                fn ($m) => (int) Donation::whereBetween('created_at', [$m->copy()->startOfMonth(), $m->copy()->endOfMonth()])->sum('amount')
            )->toArray(),
        ];

        $upcomingAppointments = Appointment::with(['client', 'services', 'schedule'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereHas('schedule', fn ($q) => $q->where('date', '>=', Carbon::today()))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentContacts = Contact::with('client')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentDonations = Donation::orderBy('created_at', 'desc')->limit(5)->get();

        $recentVolunteers = Volunteer::orderBy('created_at', 'desc')->limit(5)->get();

        $content = [
            'actualites' => Actualite::count(),
            'albums' => Album::count(),
            'masterclasses' => Masterclass::where('is_active', true)->count(),
        ];

        $siteLogo = StorageHelper::url(Setting::get('site_logo', 'images/logo.png'));

        return Inertia::render('Dashboard', [
            'kpis' => $kpis,
            'donationsMonthly' => $donationsMonthly,
            'upcomingAppointments' => $upcomingAppointments,
            'recentContacts' => $recentContacts,
            'recentDonations' => $recentDonations,
            'recentVolunteers' => $recentVolunteers,
            'content' => $content,
            'logoUrl' => $siteLogo,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Masterclass;
use App\Models\TrainingRegistration;
use App\Models\TrainingSession;
use App\Models\WaitlistEntry;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class AnalyticsController extends Controller
{
    public function index()
    {
        $totalInscriptions = TrainingRegistration::count();
        $confirmedInscriptions = TrainingRegistration::confirmed()->count();
        $confirmationRate = $totalInscriptions > 0
            ? round(($confirmedInscriptions / $totalInscriptions) * 100, 1)
            : 0;
        $totalWaitlist = WaitlistEntry::count();

        $estimatedRevenue = TrainingSession::with(['registrations' => fn ($q) => $q->confirmed()])
            ->get()
            ->sum(fn ($session) => (float) ($session->price ?? 0) * $session->registrations->count());

        // Inscriptions des 30 derniers jours (par jour)
        $days = collect(range(29, 0))->map(fn ($i) => Carbon::today()->subDays($i));
        $periodLabels = $days->map(fn ($d) => $d->format('d/m'))->toArray();

        $dailyTotal = $days->map(
            fn ($day) => TrainingRegistration::whereDate('created_at', $day)->count()
        )->toArray();

        $dailyConfirmed = $days->map(
            fn ($day) => TrainingRegistration::confirmed()->whereDate('created_at', $day)->count()
        )->toArray();

        // Masterclasses les plus populaires (par nombre d'inscriptions)
        $masterclassesData = Masterclass::with(['sessions' => fn ($q) => $q->withCount('registrations')])
            ->get()
            ->map(fn ($mc) => [
                'title' => $mc->title,
                'total' => $mc->sessions->sum('registrations_count'),
            ])
            ->sortByDesc('total')
            ->take(8)
            ->values();

        // Revenus estimés par masterclass (inscriptions confirmées × prix)
        $revenueData = Masterclass::with(['sessions' => function ($q) {
            $q->withCount(['registrations as confirmed_count' => fn ($q) => $q->confirmed()]);
        }])
            ->get()
            ->map(fn ($mc) => [
                'title' => $mc->title,
                'revenue' => $mc->sessions->sum(
                    fn ($s) => (float) ($s->price ?? 0) * ($s->confirmed_count ?? 0)
                ),
            ])
            ->sortByDesc('revenue')
            ->take(8)
            ->values();

        // Prochaines sessions
        $upcomingSessions = TrainingSession::with('masterclass')
            ->active()
            ->upcoming()
            ->orderBy('start_date')
            ->take(10)
            ->get()
            ->map(fn ($s) => [
                'id'                => $s->id,
                'masterclass_id'    => $s->masterclass_id,
                'masterclass_title' => $s->masterclass?->title ?? 'N/A',
                'start_date'        => $s->start_date->format('d/m/Y H:i'),
                'location'          => $s->location_label,
                'price'             => $s->formatted_price,
                'registrations'     => $s->registration_count,
                'max_participants'  => $s->max_participants,
                'available_spots'   => $s->available_spots,
            ]);

        return Inertia::render('Admin/Analytics', [
            'kpis' => [
                'totalInscriptions'    => $totalInscriptions,
                'confirmedInscriptions' => $confirmedInscriptions,
                'confirmationRate'     => $confirmationRate,
                'estimatedRevenue'     => $estimatedRevenue,
                'totalWaitlist'        => $totalWaitlist,
            ],
            'inscriptionsParPeriode' => [
                'labels'   => $periodLabels,
                'datasets' => [
                    [
                        'label'           => 'Total inscriptions',
                        'data'            => $dailyTotal,
                        'borderColor'     => 'rgb(99, 102, 241)',
                        'backgroundColor' => 'rgba(99, 102, 241, 0.15)',
                    ],
                    [
                        'label'           => 'Confirmées',
                        'data'            => $dailyConfirmed,
                        'borderColor'     => 'rgb(34, 197, 94)',
                        'backgroundColor' => 'rgba(34, 197, 94, 0.15)',
                    ],
                ],
            ],
            'masterclassesPopulaires' => [
                'labels' => $masterclassesData->pluck('title')->toArray(),
                'data'   => $masterclassesData->pluck('total')->toArray(),
            ],
            'revenusParMasterclass' => [
                'labels' => $revenueData->pluck('title')->toArray(),
                'data'   => $revenueData->pluck('revenue')->toArray(),
            ],
            'upcomingSessions' => $upcomingSessions,
        ]);
    }
}

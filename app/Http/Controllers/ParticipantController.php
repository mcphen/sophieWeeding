<?php

namespace App\Http\Controllers;

use App\Models\TrainingRegistration;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $query = TrainingRegistration::query()
            ->with('trainingSession.masterclass')
            ->select('email')
            ->selectRaw('MAX(name) as name')
            ->selectRaw('MAX(phone) as phone')
            ->selectRaw('COUNT(*) as total_inscriptions')
            ->selectRaw('SUM(CASE WHEN is_confirmed = 1 THEN 1 ELSE 0 END) as confirmed_count')
            ->selectRaw('MAX(created_at) as last_inscription_at')
            ->groupBy('email')
            ->orderByDesc('last_inscription_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $participants = $query->get()->map(fn($p) => [
            'email'              => $p->email,
            'name'               => $p->name,
            'phone'              => $p->phone,
            'total_inscriptions' => (int) $p->total_inscriptions,
            'confirmed_count'    => (int) $p->confirmed_count,
            'last_inscription_at' => \Carbon\Carbon::parse($p->last_inscription_at)->format('d/m/Y'),
        ]);

        return Inertia::render('Admin/Participants/Index', [
            'participants' => $participants,
            'total'        => $participants->count(),
            'search'       => $search,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Exports\NewsletterExport;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class NewsletterController extends Controller
{
    /**
     * Display a listing of the newsletter subscribers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $query = Newsletter::query();

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('email', 'like', "%{$search}%");
        }

        // Apply status filter
        if ($request->has('status') && $request->status !== null) {
            $query->where('is_active', $request->status === 'active');
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'subscribed_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $subscribers = $query->paginate(10)->withQueryString();

        return Inertia::render('Admin/Newsletter/Index', [
            'subscribers' => $subscribers,
            'filters' => $request->only(['search', 'status', 'sort_field', 'sort_direction']),
        ]);
    }

    /**
     * Export newsletter subscribers to Excel.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export()
    {
        return Excel::download(new NewsletterExport, 'abonnés-newsletter.xlsx');
    }

    /**
     * Store a new newsletter subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'email' => 'required|email|max:255',
                'source' => 'nullable|string|max:255',
            ]);

            // Check if the email already exists and is active
            $existingSubscription = Newsletter::where('email', $validated['email'])->first();

            if ($existingSubscription) {
                // If already subscribed and active
                if ($existingSubscription->is_active) {
                    return response()->json([
                        'message' => 'Vous êtes déjà inscrit à notre newsletter.',
                        'status' => 'info'
                    ]);
                }

                // If previously unsubscribed, reactivate
                $existingSubscription->update([
                    'is_active' => true,
                    'unsubscribed_at' => null,
                    'subscribed_at' => now(),
                    'source' => $validated['source'] ?? $existingSubscription->source,
                ]);

                return response()->json([
                    'message' => 'Votre inscription à notre newsletter a été réactivée.',
                    'status' => 'success'
                ]);
            }

            // Create a new subscription
            Newsletter::create([
                'email' => $validated['email'],
                'source' => $validated['source'] ?? 'website',
                'subscribed_at' => now(),
            ]);

            return response()->json([
                'message' => 'Merci pour votre inscription à notre newsletter!',
                'status' => 'success'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Veuillez entrer une adresse email valide.',
                'errors' => $e->errors(),
                'status' => 'error'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue. Veuillez réessayer plus tard.',
                'status' => 'error'
            ], 500);
        }
    }
}

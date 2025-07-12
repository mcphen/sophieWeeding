<?php

namespace App\Http\Controllers;

use App\Helpers\StorageHelper;
use App\Models\TrainingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TrainingSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trainingSessions = TrainingSession::orderBy('start_date', 'desc')
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'title' => $session->title,
                    'start_date' => $session->start_date->format('d/m/Y H:i'),
                    'end_date' => $session->end_date->format('d/m/Y H:i'),
                    'location' => $session->location,
                    'price' => $session->formatted_price,
                    'is_active' => $session->is_active,
                    'registration_count' => $session->registration_count,
                    'max_participants' => $session->max_participants,
                    'is_full' => $session->isFull(),
                ];
            });

        return Inertia::render('Admin/TrainingSessions/Index', [
            'trainingSessions' => $trainingSessions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/TrainingSessions/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'document' => 'nullable|file|mimes:pdf|max:10240',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Generate slug from title
        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $count = 1;

        // Ensure slug is unique
        while (TrainingSession::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('training-sessions', 'public');
        }

        // Handle document upload
        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('training-sessions/documents', 'public');
        }

        // Create training session
        TrainingSession::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'] ?? null,
            'image_path' => $imagePath,
            'document_path' => $documentPath,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'location' => $validated['location'] ?? null,
            'max_participants' => $validated['max_participants'] ?? 0,
            'price' => $validated['price'],
            'is_active' => $validated['is_active'] ?? true,
            'slug' => $slug,
        ]);

        return redirect()->route('admin.training-sessions.index')
            ->with('success', 'Session de formation créée avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TrainingSession $trainingSession)
    {
        return Inertia::render('Admin/TrainingSessions/Edit', [
            'trainingSession' => [
                'id' => $trainingSession->id,
                'title' => $trainingSession->title,
                'description' => $trainingSession->description,
                'content' => $trainingSession->content,
                'image_url' => $trainingSession->image_url,
                'document_url' => $trainingSession->document_url,
                'start_date' => $trainingSession->start_date->format('Y-m-d\TH:i'),
                'end_date' => $trainingSession->end_date->format('Y-m-d\TH:i'),
                'location' => $trainingSession->location,
                'max_participants' => $trainingSession->max_participants,
                'price' => $trainingSession->price,
                'is_active' => $trainingSession->is_active,
                'slug' => $trainingSession->slug,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TrainingSession $trainingSession)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'document' => 'nullable|file|mimes:pdf|max:10240',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($trainingSession->image_path && Storage::disk('public')->exists($trainingSession->image_path)) {
                Storage::disk('public')->delete($trainingSession->image_path);
            }

            // Store the new image
            $imagePath = $request->file('image')->store('training-sessions', 'public');
            $trainingSession->image_path = $imagePath;
        }

        // Handle document upload
        if ($request->hasFile('document')) {
            // Delete old document if it exists
            if ($trainingSession->document_path && Storage::disk('public')->exists($trainingSession->document_path)) {
                Storage::disk('public')->delete($trainingSession->document_path);
            }

            // Store the new document
            $documentPath = $request->file('document')->store('training-sessions/documents', 'public');
            $trainingSession->document_path = $documentPath;
        }

        // Update training session
        $trainingSession->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'location' => $validated['location'] ?? null,
            'max_participants' => $validated['max_participants'] ?? 0,
            'price' => $validated['price'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.training-sessions.index')
            ->with('success', 'Session de formation mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrainingSession $trainingSession)
    {
        // Delete image if it exists
        if ($trainingSession->image_path && Storage::disk('public')->exists($trainingSession->image_path)) {
            Storage::disk('public')->delete($trainingSession->image_path);
        }

        // Delete document if it exists
        if ($trainingSession->document_path && Storage::disk('public')->exists($trainingSession->document_path)) {
            Storage::disk('public')->delete($trainingSession->document_path);
        }

        $trainingSession->delete();

        return redirect()->route('admin.training-sessions.index')
            ->with('success', 'Session de formation supprimée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TrainingSession $trainingSession)
    {
        $registrations = $trainingSession->registrations()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($registration) {
                return [
                    'id' => $registration->id,
                    'name' => $registration->name,
                    'email' => $registration->email,
                    'phone' => $registration->phone,
                    'message' => $registration->message,
                    'is_confirmed' => $registration->is_confirmed,
                    'confirmed_at' => $registration->confirmed_at ? $registration->confirmed_at->format('d/m/Y H:i') : null,
                    'created_at' => $registration->created_at->format('d/m/Y H:i'),
                ];
            });

        return Inertia::render('Admin/TrainingSessions/Show', [
            'trainingSession' => [
                'id' => $trainingSession->id,
                'title' => $trainingSession->title,
                'description' => $trainingSession->description,
                'content' => $trainingSession->content,
                'image_url' => $trainingSession->image_url,
                'document_url' => $trainingSession->document_url,
                'start_date' => $trainingSession->start_date->format('d/m/Y H:i'),
                'end_date' => $trainingSession->end_date->format('d/m/Y H:i'),
                'location' => $trainingSession->location,
                'max_participants' => $trainingSession->max_participants,
                'price' => $trainingSession->formatted_price,
                'is_active' => $trainingSession->is_active,
                'registration_count' => $trainingSession->registration_count,
                'available_spots' => $trainingSession->available_spots,
                'is_full' => $trainingSession->isFull(),
            ],
            'registrations' => $registrations,
        ]);
    }
}

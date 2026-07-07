<?php

namespace App\Http\Controllers;

use App\Mail\NewMasterclassAnnouncementMail;
use App\Models\Masterclass;
use App\Models\TrainingRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MasterclassController extends Controller
{
    public function index()
    {
        $masterclasses = Masterclass::withCount('sessions')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($mc) => [
                'id' => $mc->id,
                'title' => $mc->title,
                'niveau' => $mc->niveau,
                'image_url' => $mc->image_url,
                'is_active' => $mc->is_active,
                'sessions_count' => $mc->sessions_count,
                'upcoming_sessions_count' => $mc->upcoming_sessions_count,
                'slug' => $mc->slug,
            ]);

        return Inertia::render('Admin/Masterclasses/Index', [
            'masterclasses' => $masterclasses,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Masterclasses/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'niveau'         => 'required|string|max:255',
            'description'    => 'nullable|string',
            'programme'      => 'nullable|string',
            'image'          => 'nullable|image|max:2048',
            'document'       => 'nullable|file|mimes:pdf|max:10240',
            'is_active'      => 'boolean',
            'send_announce'  => 'boolean',
            'manual_emails'  => 'nullable|string',
        ]);

        $slug = $this->uniqueSlug(Str::slug($validated['title']));

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('masterclasses', 'public')
            : null;

        $documentPath = $request->hasFile('document')
            ? $request->file('document')->store('masterclasses/documents', 'public')
            : null;

        $masterclass = Masterclass::create([
            'title'         => $validated['title'],
            'niveau'        => $validated['niveau'],
            'description'   => $validated['description'] ?? null,
            'programme'     => $validated['programme'] ?? null,
            'image_path'    => $imagePath,
            'document_path' => $documentPath,
            'slug'          => $slug,
            'is_active'     => $validated['is_active'] ?? true,
        ]);

        $sentCount = 0;

        if (!empty($validated['send_announce'])) {
            $sentCount = $this->sendAnnouncement($masterclass, true, $validated['manual_emails'] ?? '');
        }

        $message = 'Masterclass créée avec succès.';
        if ($sentCount > 0) {
            $message .= " Annonce envoyée à {$sentCount} destinataire(s).";
        }

        return redirect()->route('admin.masterclasses.index')->with('success', $message);
    }

    public function announce(Request $request, Masterclass $masterclass)
    {
        $request->validate([
            'include_past_participants' => 'boolean',
            'manual_emails'            => 'nullable|string',
            'email_list_ids'           => 'nullable|array',
            'email_list_ids.*'         => 'integer|exists:email_lists,id',
        ]);

        $sentCount = $this->sendAnnouncement(
            $masterclass,
            !empty($request->boolean('include_past_participants')),
            (string) $request->input('manual_emails', ''),
            $request->input('email_list_ids', [])
        );

        return redirect()->back()->with('success', "Annonce envoyée à {$sentCount} destinataire(s).");
    }

    public function show(Masterclass $masterclass)
    {
        $sessions = $masterclass->sessions()
            ->orderBy('start_date', 'asc')
            ->get()
            ->map(fn($s) => [
                'id'                  => $s->id,
                'start_date'          => $s->start_date->format('d/m/Y H:i'),
                'end_date'            => $s->end_date ? $s->end_date->format('d/m/Y H:i') : null,
                'start_date_form'     => $s->start_date->format('Y-m-d\TH:i'),
                'end_date_form'       => $s->end_date ? $s->end_date->format('Y-m-d\TH:i') : null,
                'location_type'       => $s->location_type,
                'location_label'      => $s->location_label,
                'adresse'             => $s->adresse,
                'google_maps_url'     => $s->google_maps_url,
                'online_link'         => $s->online_link,
                'price'               => $s->formatted_price,
                'price_raw'           => $s->price,
                'max_participants'    => $s->max_participants,
                'registration_count'  => $s->registration_count,
                'available_spots'     => $s->available_spots,
                'is_active'           => $s->is_active,
                'is_full'             => $s->isFull(),
                'is_past'             => $s->start_date->isPast(),
            ]);

        $emailLists = \App\Models\EmailList::withCount('entries')
            ->orderBy('name')
            ->get()
            ->map(fn($l) => [
                'id'            => $l->id,
                'name'          => $l->name,
                'entries_count' => $l->entries_count,
            ]);

        return Inertia::render('Admin/Masterclasses/Show', [
            'masterclass' => [
                'id'           => $masterclass->id,
                'title'        => $masterclass->title,
                'niveau'       => $masterclass->niveau,
                'description'  => $masterclass->description,
                'programme'    => $masterclass->programme,
                'image_url'    => $masterclass->image_url,
                'document_url' => $masterclass->document_url,
                'is_active'    => $masterclass->is_active,
                'slug'         => $masterclass->slug,
            ],
            'sessions'    => $sessions,
            'emailLists'  => $emailLists,
        ]);
    }

    public function edit(Masterclass $masterclass)
    {
        return Inertia::render('Admin/Masterclasses/Edit', [
            'masterclass' => [
                'id'           => $masterclass->id,
                'title'        => $masterclass->title,
                'niveau'       => $masterclass->niveau,
                'description'  => $masterclass->description,
                'programme'    => $masterclass->programme,
                'image_url'    => $masterclass->image_url,
                'document_url' => $masterclass->document_url,
                'is_active'    => $masterclass->is_active,
                'slug'         => $masterclass->slug,
            ],
        ]);
    }

    public function update(Request $request, Masterclass $masterclass)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'niveau'      => 'required|string|max:255',
            'description' => 'nullable|string',
            'programme'   => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
            'document'    => 'nullable|file|mimes:pdf|max:10240',
            'is_active'   => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($masterclass->image_path) {
                Storage::disk('public')->delete($masterclass->image_path);
            }
            $masterclass->image_path = $request->file('image')->store('masterclasses', 'public');
        }

        if ($request->hasFile('document')) {
            if ($masterclass->document_path) {
                Storage::disk('public')->delete($masterclass->document_path);
            }
            $masterclass->document_path = $request->file('document')->store('masterclasses/documents', 'public');
        }

        $masterclass->update([
            'title'       => $validated['title'],
            'niveau'      => $validated['niveau'],
            'description' => $validated['description'] ?? null,
            'programme'   => $validated['programme'] ?? null,
            'is_active'   => $validated['is_active'] ?? true,
        ]);

        if ($masterclass->wasChanged('title')) {
            $masterclass->update(['slug' => $this->uniqueSlug(Str::slug($validated['title']), $masterclass->id)]);
        }

        return redirect()->route('admin.masterclasses.show', $masterclass)
            ->with('success', 'Masterclass mise à jour avec succès.');
    }

    public function destroy(Masterclass $masterclass)
    {
        if ($masterclass->image_path) {
            Storage::disk('public')->delete($masterclass->image_path);
        }
        if ($masterclass->document_path) {
            Storage::disk('public')->delete($masterclass->document_path);
        }

        $masterclass->delete();

        return redirect()->route('admin.masterclasses.index')
            ->with('success', 'Masterclass supprimée avec succès.');
    }

    private function sendAnnouncement(Masterclass $masterclass, bool $includePastParticipants, string $manualEmailsRaw, array $emailListIds = []): int
    {
        $recipients = collect();

        if ($includePastParticipants) {
            $past = TrainingRegistration::query()
                ->select('email')
                ->selectRaw('MAX(name) as name')
                ->groupBy('email')
                ->get()
                ->mapWithKeys(fn($r) => [$r->email => $r->name]);

            $recipients = $recipients->union($past);
        }

        if (!empty($emailListIds)) {
            $listEmails = \App\Models\EmailListEntry::whereIn('email_list_id', $emailListIds)
                ->get()
                ->mapWithKeys(fn($e) => [$e->email => $e->name ?? '']);

            $recipients = $recipients->union($listEmails);
        }

        if ($manualEmailsRaw !== '') {
            $manual = collect(preg_split('/[\s,;]+/', trim($manualEmailsRaw)))
                ->map(fn($e) => trim($e))
                ->filter(fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
                ->unique()
                ->mapWithKeys(fn($e) => [$e => '']);

            $recipients = $recipients->union($manual);
        }

        $sentCount = 0;
        foreach ($recipients as $email => $name) {
            try {
                Mail::to($email)->send(new NewMasterclassAnnouncementMail($masterclass, (string) $name));
                $sentCount++;
            } catch (\Exception) {
                // Continue même si un envoi échoue
            }
        }

        return $sentCount;
    }

    private function uniqueSlug(string $base, ?int $excludeId = null): string
    {
        $slug = $base;
        $count = 1;
        while (Masterclass::where('slug', $slug)->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = $base . '-' . $count++;
        }
        return $slug;
    }
}

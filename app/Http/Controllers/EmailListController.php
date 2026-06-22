<?php

namespace App\Http\Controllers;

use App\Exports\EmailListTemplateExport;
use App\Imports\EmailListImport;
use App\Models\EmailList;
use App\Models\EmailListEntry;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class EmailListController extends Controller
{
    public function index()
    {
        $lists = EmailList::withCount('entries')
            ->orderBy('name')
            ->get()
            ->map(fn($list) => [
                'id'           => $list->id,
                'name'         => $list->name,
                'description'  => $list->description,
                'entries_count' => $list->entries_count,
                'entries'      => $list->entries()->orderBy('email')->get()->map(fn($e) => [
                    'id'    => $e->id,
                    'email' => $e->email,
                    'name'  => $e->name,
                    'phone' => $e->phone,
                ]),
            ]);

        return Inertia::render('Admin/EmailLists/Index', [
            'lists' => $lists,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:email_lists,name',
            'description' => 'nullable|string|max:500',
        ]);

        EmailList::create($validated);

        return redirect()->back()->with('success', 'Liste créée avec succès.');
    }

    public function update(Request $request, EmailList $emailList)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:email_lists,name,' . $emailList->id,
            'description' => 'nullable|string|max:500',
        ]);

        $emailList->update($validated);

        return redirect()->back()->with('success', 'Liste mise à jour.');
    }

    public function destroy(EmailList $emailList)
    {
        $emailList->delete();

        return redirect()->back()->with('success', 'Liste supprimée.');
    }

    public function addEntries(Request $request, EmailList $emailList)
    {
        $request->validate([
            'emails_raw' => 'required|string',
        ]);

        $emails = collect(preg_split('/[\s,;]+/', trim($request->emails_raw)))
            ->map(fn($e) => trim($e))
            ->filter(fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
            ->unique();

        $added = 0;
        foreach ($emails as $email) {
            $emailList->entries()->firstOrCreate(['email' => strtolower($email)]);
            $added++;
        }

        return redirect()->back()->with('success', "{$added} email(s) ajouté(s).");
    }

    public function removeEntry(EmailList $emailList, EmailListEntry $entry)
    {
        abort_if($entry->email_list_id !== $emailList->id, 403);
        $entry->delete();

        return redirect()->back()->with('success', 'Email retiré.');
    }

    public function downloadTemplate()
    {
        return Excel::download(new EmailListTemplateExport(), 'modele_contacts.xlsx');
    }

    public function importEntries(Request $request, EmailList $emailList)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $import = new EmailListImport($emailList);
        Excel::import($import, $request->file('file'));

        $msg = "{$import->added} contact(s) importé(s)";
        if ($import->skipped > 0) {
            $msg .= ", {$import->skipped} ignoré(s) (déjà présents ou invalides)";
        }
        $msg .= '.';

        return redirect()->back()->with('success', $msg);
    }
}

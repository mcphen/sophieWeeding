<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TeamMemberController extends Controller
{
    public function index()
    {
        $members = TeamMember::orderBy('ordre')->paginate(10);
        return Inertia::render('Admin/TeamMembers/TeamMembersIndex', [
            'members' => $members,
        ]);
    }

    public function getListeDatas(){

        $members = TeamMember::query()->orderBy('ordre')->get();

        return response()->json($members);
    }

    public function create()
    {
        return Inertia::render('Admin/TeamMembers/TeamMembersCreate');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'firstname' => 'required|string|max:100',
            'lastname'  => 'required|string|max:100',
            'position'  => 'required|string|max:150',
            'ordre'     => 'nullable|integer|min:0',
            'bio'       => 'nullable|string',
            'image'     => 'nullable|mimes:jpeg,jpg,png,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('team_members', 'public');
        }

        TeamMember::create($data);

        return redirect()->route('admin.team-members.index')
            ->with('success', 'Membre d\'équipe créé.');
    }

    public function edit(TeamMember $team_member)
    {
        return Inertia::render('Admin/TeamMembers/TeamMembersEdit', [
            'member' => $team_member,
        ]);
    }

    public function update(Request $request, TeamMember $team_member)
    {
        $data = $request->validate([
            'firstname' => 'required|string|max:100',
            'lastname'  => 'required|string|max:100',
            'position'  => 'required|string|max:150',
            'ordre'     => 'nullable|integer|min:0',
            'bio'       => 'nullable|string',
            'image'     => 'nullable|mimes:jpeg,jpg,png,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($team_member->image_path) {
                Storage::disk('public')->delete($team_member->image_path);
            }
            $data['image_path'] = $request->file('image')->store('team_members', 'public');
        }

        $team_member->update($data);

        return redirect()->route('admin.team-members.index')
            ->with('success', 'Membre d\'équipe mis à jour.');
    }

    public function destroy(TeamMember $team_member)
    {
        if ($team_member->image_path) {
            Storage::disk('public')->delete($team_member->image_path);
        }
        $team_member->delete();

        return redirect()->route('admin.team-members.index')
            ->with('success', 'Membre d\'équipe supprimé.');
    }
}

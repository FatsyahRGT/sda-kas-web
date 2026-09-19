<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $groupId = $request->filled('group_id') ? (int) $request->group_id : null;
        $search = $request->input('search');

        $groups = Group::orderBy('name')->get();

        $members = Member::with('group')
            ->when($groupId, fn ($q) => $q->where('group_id', $groupId))
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('members.index', compact('members', 'groups', 'groupId', 'search'));
    }

    public function create(): View
    {
        $groups = Group::orderBy('name')->get();

        return view('members.create', compact('groups'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'group_id' => ['required', 'exists:groups,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ]);

        Member::create([
            'group_id' => $validated['group_id'],
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('members.index', ['group_id' => $validated['group_id']])
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function edit(Member $member): View
    {
        $groups = Group::orderBy('name')->get();

        return view('members.edit', compact('member', 'groups'));
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $validated = $request->validate([
            'group_id' => ['required', 'exists:groups,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ]);

        $member->update([
            'group_id' => $validated['group_id'],
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('members.index', ['group_id' => $member->group_id])
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $groupId = $member->group_id;
        $member->delete();

        return redirect()->route('members.index', ['group_id' => $groupId])
            ->with('success', 'Anggota berhasil dihapus.');
    }
}

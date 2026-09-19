<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function index(): View
    {
        $groups = Group::withCount(['members', 'periods'])
            ->with(['creator'])
            ->orderBy('name')
            ->paginate(15);

        return view('groups.index', compact('groups'));
    }

    public function create(): View
    {
        return view('groups.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:groups,slug'],
            'description' => ['nullable', 'string'],
            'default_due_amount' => ['required', 'numeric', 'min:0'],
            'is_public' => ['boolean'],
        ]);

        $slug = ! empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        // Ensure unique slug
        $originalSlug = $slug;
        $count = 1;
        while (Group::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        Group::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'default_due_amount' => $validated['default_due_amount'],
            'is_public' => $request->boolean('is_public'),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('groups.index')->with('success', 'Grup kas berhasil dibuat.');
    }

    public function edit(Group $group): View
    {
        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('groups', 'slug')->ignore($group->id)],
            'description' => ['nullable', 'string'],
            'default_due_amount' => ['required', 'numeric', 'min:0'],
            'is_public' => ['boolean'],
        ]);

        $group->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'description' => $validated['description'] ?? null,
            'default_due_amount' => $validated['default_due_amount'],
            'is_public' => $request->boolean('is_public'),
        ]);

        return redirect()->route('groups.index')->with('success', 'Grup kas berhasil diperbarui.');
    }

    public function togglePublic(Group $group): RedirectResponse
    {
        $group->update([
            'is_public' => ! $group->is_public,
        ]);

        $status = $group->is_public ? 'dibuka untuk publik' : 'dijadikan privat';

        return back()->with('success', "Grup {$group->name} sekarang {$status}.");
    }

    public function destroy(Group $group): RedirectResponse
    {
        $group->delete();

        return redirect()->route('groups.index')->with('success', 'Grup kas berhasil dihapus.');
    }
}

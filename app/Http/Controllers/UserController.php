<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::withCount(['tokens'])->orderBy('name')->get();

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'superadmin'])],
            'is_active' => ['boolean'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'superadmin'])],
            'is_active' => ['boolean'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active'),
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function generateToken(Request $request, User $user): RedirectResponse
    {
        // Revoke existing automation tokens
        $user->tokens()->delete();

        // Create new Sanctum personal access token
        $tokenName = 'automation-'.Str::slug($user->name).'-'.now()->format('YmdHis');
        $token = $user->createToken($tokenName, ['*']);

        // Also update api_token column for reference if needed
        $user->update(['api_token' => hash('sha256', $token->plainTextToken)]);

        return back()->with('plainTextToken', $token->plainTextToken)
            ->with('success', "API Token baru berhasil dibuat untuk user {$user->name}. Simpan token ini sekarang karena tidak akan ditampilkan lagi.");
    }

    public function revokeTokens(User $user): RedirectResponse
    {
        $user->tokens()->delete();
        $user->update(['api_token' => null]);

        return back()->with('success', "Semua API token untuk {$user->name} berhasil dicabut.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
        }

        $user->tokens()->delete();
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}

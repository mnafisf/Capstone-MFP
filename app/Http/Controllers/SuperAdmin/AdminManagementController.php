<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreAdminRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminManagementController extends Controller
{
    public function index(Request $request): View
    {
        $admins = User::query()
            ->whereHas('role', fn ($q) => $q->where('slug', 'admin'))
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('super-admin.admins.index', compact('admins'));
    }

    public function create(): View
    {
        return view('super-admin.admins.create');
    }

    public function store(StoreAdminRequest $request): RedirectResponse
    {
        $adminRole = Role::query()->where('slug', 'admin')->firstOrFail();

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $adminRole->id,
        ]);

        return redirect()
            ->route('super-admin.admins.index')
            ->with('success', 'Admin berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        abort_unless(optional($user->role)->slug === 'admin', 404);

        return view('super-admin.admins.edit', compact('user'));
    }

    public function update(StoreAdminRequest $request, User $user): RedirectResponse
    {
        abort_unless(optional($user->role)->slug === 'admin', 404);

        $payload = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $payload['password'] = Hash::make($request->password);
        }

        $user->update($payload);

        return redirect()
            ->route('super-admin.admins.index')
            ->with('success', 'Data admin berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless(optional($user->role)->slug === 'admin', 404);

        $user->delete();

        return redirect()
            ->route('super-admin.admins.index')
            ->with('success', 'Admin berhasil dihapus.');
    }
}

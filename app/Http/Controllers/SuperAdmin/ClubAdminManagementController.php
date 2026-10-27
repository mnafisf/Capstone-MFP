<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreClubAdminRequest;
use App\Models\Club;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ClubAdminManagementController extends Controller
{
    public function index(Request $request): View
    {
        $clubAdmins = User::query()
            ->with('club')
            ->whereHas('role', fn ($q) => $q->where('slug', 'club-admin'))
            ->when($request->search, function ($query) use ($request): void {
                $search = '%'.$request->search.'%';
                $query->where(function ($inner) use ($search): void {
                    $inner->where('name', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhereHas('club', fn ($club) => $club->where('name', 'like', $search));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('super-admin.club-admins.index', compact('clubAdmins'));
    }

    public function create(): View
    {
        return view('super-admin.club-admins.create');
    }

    public function store(StoreClubAdminRequest $request): RedirectResponse
    {
        $clubAdminRole = Role::query()->where('slug', 'club-admin')->firstOrFail();

        DB::transaction(function () use ($request, $clubAdminRole): void {
            $clubPayload = [
                'name' => $request->string('club_name')->toString(),
                'code' => $request->string('club_code')->toString(),
                'is_active' => (bool) $request->boolean('club_is_active', true),
            ];

            if ($this->supportsClubLogo() && $request->hasFile('club_logo')) {
                $clubPayload['logo_path'] = $request->file('club_logo')->store('club-logos', 'public');
            }

            $club = Club::create($clubPayload);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $clubAdminRole->id,
                'club_id' => $club->id,
            ]);
        });

        return redirect()
            ->route('super-admin.club-admins.index')
            ->with('success', 'Admin klub berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        abort_unless(optional($user->role)->slug === 'club-admin', 404);

        $user->load('club');

        return view('super-admin.club-admins.edit', compact('user'));
    }

    public function update(StoreClubAdminRequest $request, User $user): RedirectResponse
    {
        abort_unless(optional($user->role)->slug === 'club-admin', 404);

        DB::transaction(function () use ($request, $user): void {
            $club = $user->club;
            abort_unless($club, 404);

            $clubPayload = [
                'name' => $request->string('club_name')->toString(),
                'code' => $request->string('club_code')->toString(),
                'is_active' => (bool) $request->boolean('club_is_active', true),
            ];

            if ($this->supportsClubLogo() && $request->hasFile('club_logo')) {
                if ($club->logo_path ?? null) {
                    Storage::disk('public')->delete($club->logo_path);
                }
                $clubPayload['logo_path'] = $request->file('club_logo')->store('club-logos', 'public');
            }

            $club->update($clubPayload);

            $payload = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $payload['password'] = Hash::make($request->password);
            }

            $user->update($payload);
        });

        return redirect()
            ->route('super-admin.club-admins.index')
            ->with('success', 'Data admin klub berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless(optional($user->role)->slug === 'club-admin', 404);

        $club = $user->club;
        $user->delete();

        if ($club && $club->users()->doesntExist()) {
            if ($this->supportsClubLogo() && ($club->logo_path ?? null)) {
                Storage::disk('public')->delete($club->logo_path);
            }
            $club->delete();
        }

        return redirect()
            ->route('super-admin.club-admins.index')
            ->with('success', 'Admin klub berhasil dihapus.');
    }

    private function supportsClubLogo(): bool
    {
        return Schema::hasColumn('clubs', 'logo_path');
    }
}

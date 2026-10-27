<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function impersonate(User $user)
    {
        $current = Auth::user();
        if (optional($current->role)->slug !== 'super-admin') {
            abort(403);
        }

        session(['impersonator_id' => $current->id]);
        Auth::loginUsingId($user->id);

        return redirect()->route('dashboard')->with('status', 'Masuk sebagai '.$user->name);
    }

    public function stop(Request $request)
    {
        $impersonatorId = session()->pull('impersonator_id');
        if ($impersonatorId) {
            Auth::loginUsingId($impersonatorId);
        }

        return redirect()->route('super-admin.admins.index')->with('status', 'Kembali ke akun Super Admin.');
    }
}

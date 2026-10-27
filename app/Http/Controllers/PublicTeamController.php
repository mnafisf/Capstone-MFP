<?php

namespace App\Http\Controllers;

use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicTeamController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $clubsQuery = Club::query()
            ->where('is_active', true)
            ->withCount(['players', 'officials'])
            ->orderBy('name');

        if ($search !== '') {
            $clubsQuery->where(function ($query) use ($search): void {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('code', 'like', '%'.$search.'%');
            });
        }

        $clubs = $clubsQuery->paginate(16);

        return view('public.teams.index', [
            'clubs' => $clubs,
            'search' => $search,
        ]);
    }
}

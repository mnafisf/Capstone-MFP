<?php

namespace App\Providers;

use App\Models\Club;
use App\Models\Event;
use App\Models\Player;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('partials.public.footer', function ($view): void {
            if (! Schema::hasTable('events') || ! Schema::hasTable('clubs') || ! Schema::hasTable('players')) {
                $view->with([
                    'featuredEvent' => null,
                    'totalClubs' => 0,
                    'totalPlayers' => 0,
                ]);

                return;
            }

            $featuredEvent = Event::query()
                ->whereDate('start_date', '<=', now()->toDateString())
                ->whereDate('end_date', '>=', now()->toDateString())
                ->orderByDesc('start_date')
                ->first() ?? Event::query()->orderByDesc('start_date')->first();

            $view->with([
                'featuredEvent' => $featuredEvent,
                'totalClubs' => Club::count(),
                'totalPlayers' => Player::count(),
            ]);
        });
    }
}

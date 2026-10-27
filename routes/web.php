<?php

use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\EventParticipantController;
use App\Http\Controllers\Admin\MatchController;
use App\Http\Controllers\Admin\OfficialVerificationController;
use App\Http\Controllers\Admin\PlayerVerificationController;
use App\Http\Controllers\Admin\ClubCompletenessController;
use App\Http\Controllers\ClubAdmin\EventRegistrationController;
use App\Http\Controllers\ClubAdmin\OfficialController;
use App\Http\Controllers\ClubAdmin\LineupController;
use App\Http\Controllers\ClubAdmin\PlayerController;
use App\Http\Controllers\ClubAdmin\TransferController;
use App\Http\Controllers\ClubAdmin\ClubColorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicHomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperAdmin\AdminManagementController;
use App\Http\Controllers\SuperAdmin\ClubAdminManagementController;
use App\Http\Controllers\SuperAdmin\ClubManagementController;
use App\Http\Controllers\SuperAdmin\CompetitionSettingController;
use App\Http\Controllers\SuperAdmin\RefereeController;
use App\Http\Controllers\SuperAdmin\TransferVerificationController;
use App\Http\Controllers\SuperAdmin\StadiumController;
use App\Http\Controllers\SuperAdmin\ImpersonationController;
use Illuminate\Support\Facades\Route;

Route::get('/', PublicHomeController::class)->name('home');
Route::get('/events', [\App\Http\Controllers\PublicEventController::class, 'index'])->name('public.events.index');
Route::get('/events/{event}', [\App\Http\Controllers\PublicEventController::class, 'show'])->name('public.events.show');
Route::get('/teams', [\App\Http\Controllers\PublicTeamController::class, 'index'])->name('public.teams.index');
Route::get('/standings', [\App\Http\Controllers\PublicStandingsController::class, 'index'])->name('public.standings.index');

Route::get('/dashboard', DashboardController::class)->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('super-admin')->name('super-admin.')->middleware('role:super-admin')->group(function () {
        Route::get('/admins', [AdminManagementController::class, 'index'])->name('admins.index');
        Route::get('/admins/create', [AdminManagementController::class, 'create'])->name('admins.create');
        Route::post('/admins', [AdminManagementController::class, 'store'])->name('admins.store');
        Route::get('/admins/{user}/edit', [AdminManagementController::class, 'edit'])->name('admins.edit');
        Route::put('/admins/{user}', [AdminManagementController::class, 'update'])->name('admins.update');
        Route::delete('/admins/{user}', [AdminManagementController::class, 'destroy'])->name('admins.destroy');

        Route::get('/club-admins', [ClubAdminManagementController::class, 'index'])->name('club-admins.index');
        Route::get('/club-admins/create', [ClubAdminManagementController::class, 'create'])->name('club-admins.create');
        Route::post('/club-admins', [ClubAdminManagementController::class, 'store'])->name('club-admins.store');
        Route::get('/club-admins/{user}/edit', [ClubAdminManagementController::class, 'edit'])->name('club-admins.edit');
        Route::put('/club-admins/{user}', [ClubAdminManagementController::class, 'update'])->name('club-admins.update');
        Route::delete('/club-admins/{user}', [ClubAdminManagementController::class, 'destroy'])->name('club-admins.destroy');

        Route::get('/clubs', [ClubManagementController::class, 'index'])->name('clubs.index');
        Route::get('/clubs/{club}/logo', [ClubManagementController::class, 'logo'])->name('clubs.logo');
        Route::post('/clubs', [ClubManagementController::class, 'store'])->name('clubs.store');
        Route::put('/clubs/{club}', [ClubManagementController::class, 'update'])->name('clubs.update');
        Route::patch('/clubs/{club}/toggle', [ClubManagementController::class, 'toggle'])->name('clubs.toggle');
        Route::delete('/clubs/{club}', [ClubManagementController::class, 'destroy'])->name('clubs.destroy');

        Route::get('/stadiums', [StadiumController::class, 'index'])->name('stadiums.index');
        Route::get('/stadiums/create', [StadiumController::class, 'create'])->name('stadiums.create');
        Route::post('/stadiums', [StadiumController::class, 'store'])->name('stadiums.store');
        Route::get('/stadiums/{stadium}/edit', [StadiumController::class, 'edit'])->name('stadiums.edit');
        Route::put('/stadiums/{stadium}', [StadiumController::class, 'update'])->name('stadiums.update');
        Route::patch('/stadiums/{stadium}/toggle', [StadiumController::class, 'toggle'])->name('stadiums.toggle');
        Route::delete('/stadiums/{stadium}', [StadiumController::class, 'destroy'])->name('stadiums.destroy');

        Route::get('/referees', [RefereeController::class, 'index'])->name('referees.index');
        Route::get('/referees/create', [RefereeController::class, 'create'])->name('referees.create');
        Route::post('/referees', [RefereeController::class, 'store'])->name('referees.store');
        Route::get('/referees/{referee}', [RefereeController::class, 'show'])->name('referees.show');
        Route::put('/referees/{referee}', [RefereeController::class, 'update'])->name('referees.update');
        Route::delete('/referees/{referee}', [RefereeController::class, 'destroy'])->name('referees.destroy');

        Route::get('/transfers/verification', [TransferVerificationController::class, 'index'])->name('transfers.verification.index');
        Route::get('/transfers/{transfer}/preview', [TransferVerificationController::class, 'preview'])->name('transfers.preview');
        Route::patch('/transfers/{transfer}/verification', [TransferVerificationController::class, 'update'])->name('transfers.verification.update');

        Route::get('/settings', [CompetitionSettingController::class, 'index'])->name('settings.index');
        Route::get('/settings/{match}/deadline', [CompetitionSettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings/{match}/deadline', [CompetitionSettingController::class, 'update'])->name('settings.update');
        
        Route::post('/impersonate/{user}', [ImpersonationController::class, 'impersonate'])->name('impersonate');
    });

    // allow any authenticated user to stop impersonation (so impersonated users can return)
    Route::post('/impersonate/stop', [ImpersonationController::class, 'stop'])->name('super-admin.impersonate.stop');

    Route::prefix('admin')->name('admin.')->middleware('role:admin,super-admin')->group(function () {
        Route::get('/events', [EventController::class, 'index'])->name('events.index');
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
        Route::patch('/events/{event}/toggle', [EventController::class, 'toggle'])->name('events.toggle');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

        Route::get('/events/{event}/teams', [EventParticipantController::class, 'index'])->name('events.teams.index');
        Route::post('/events/{event}/participants', [EventParticipantController::class, 'store'])->name('events.participants.store');
        Route::post('/events/{event}/bracket', [EventParticipantController::class, 'generateBracket'])->name('events.bracket.generate');
        Route::patch('/events/{event}/bracket/slots', [EventParticipantController::class, 'updateSlots'])->name('events.bracket.slots');
        Route::patch('/events/{event}/bracket/matches/{match}', [EventParticipantController::class, 'recordWinner'])->name('events.bracket.winner');
        Route::get('/events/{event}/bracket/matches/{match}/schedule', [MatchController::class, 'createFromBracket'])->name('events.bracket.schedule');
        Route::delete('/events/{event}/bracket', [EventParticipantController::class, 'destroyBracket'])->name('events.bracket.destroy');

        Route::get('/matches', [MatchController::class, 'index'])->name('matches.index');
        Route::post('/matches', [MatchController::class, 'store'])->name('matches.store');
        Route::get('/matches/{match}', [MatchController::class, 'show'])->name('matches.show');
        Route::put('/matches/{match}', [MatchController::class, 'update'])->name('matches.update');
        Route::delete('/matches/{match}', [MatchController::class, 'destroy'])->name('matches.destroy');
        Route::get('/matches/{match}/dsp-pdf', [MatchController::class, 'downloadDsp'])->name('matches.dsp.download');
        Route::patch('/matches/{match}/summary', [MatchController::class, 'updateSummary'])->name('matches.summary.update');
        Route::get('/matches/{match}/summary-pdf', [MatchController::class, 'downloadSummary'])->name('matches.summary.download');
        Route::post('/matches/{match}/goals', [MatchController::class, 'storeGoal'])->name('matches.goals.store');
        Route::delete('/matches/{match}/goals/{goal}', [MatchController::class, 'destroyGoal'])->name('matches.goals.destroy');
        Route::post('/matches/{match}/cards', [MatchController::class, 'storeCard'])->name('matches.cards.store');
        Route::delete('/matches/{match}/cards/{card}', [MatchController::class, 'destroyCard'])->name('matches.cards.destroy');
        Route::post('/matches/{match}/substitutions', [MatchController::class, 'storeSubstitution'])->name('matches.substitutions.store');
        Route::delete('/matches/{match}/substitutions/{substitution}', [MatchController::class, 'destroySubstitution'])->name('matches.substitutions.destroy');
        Route::get('/monitoring', [MatchController::class, 'monitoring'])->name('monitoring.index');

        Route::get('/players/verification', [PlayerVerificationController::class, 'index'])->name('verification.index');
        Route::get('/players/{player}/documents/{field}', [PlayerVerificationController::class, 'previewDocument'])->name('verification.documents.preview');
        Route::patch('/players/{player}/verification', [PlayerVerificationController::class, 'update'])->name('verification.update');

        Route::get('/officials/verification', [OfficialVerificationController::class, 'index'])->name('officials.verification.index');
        Route::patch('/officials/{official}/verification', [OfficialVerificationController::class, 'update'])->name('officials.verification.update');

        Route::get('/club-completeness', [ClubCompletenessController::class, 'index'])->name('club-completeness.index');
        Route::get('/club-completeness/{club}', [ClubCompletenessController::class, 'show'])->name('club-completeness.show');
        Route::get('/club-completeness/{club}/players/{player}', [ClubCompletenessController::class, 'playerShow'])->name('club-completeness.players.show');
        Route::delete('/club-completeness/{club}/players/{player}', [ClubCompletenessController::class, 'playerDestroy'])->name('club-completeness.players.destroy');
        Route::get('/club-completeness/{club}/officials/{official}', [ClubCompletenessController::class, 'officialShow'])->name('club-completeness.officials.show');
        Route::delete('/club-completeness/{club}/officials/{official}', [ClubCompletenessController::class, 'officialDestroy'])->name('club-completeness.officials.destroy');
    });

    Route::prefix('club-admin')->name('club-admin.')->middleware('role:club-admin')->group(function () {
        Route::get('/players', [PlayerController::class, 'index'])->name('players.index');
        Route::post('/players', [PlayerController::class, 'store'])->name('players.store');
        Route::put('/players/{player}', [PlayerController::class, 'update'])->name('players.update');
        Route::delete('/players/{player}', [PlayerController::class, 'destroy'])->name('players.destroy');

        Route::get('/officials', [OfficialController::class, 'index'])->name('officials.index');
        Route::post('/officials', [OfficialController::class, 'store'])->name('officials.store');
        Route::put('/officials/{official}', [OfficialController::class, 'update'])->name('officials.update');
        Route::delete('/officials/{official}', [OfficialController::class, 'destroy'])->name('officials.destroy');

        // Club colors (jersey)
        Route::get('/club/colors', [ClubColorController::class, 'edit'])->name('club.colors.edit');
        Route::put('/club/colors', [ClubColorController::class, 'update'])->name('club.colors.update');

        Route::get('/events', [EventRegistrationController::class, 'index'])->name('events.index');
        Route::post('/events/registration', [EventRegistrationController::class, 'store'])->name('events.register');

        Route::get('/lineups', [LineupController::class, 'index'])->name('lineups.index');
        Route::post('/lineups', [LineupController::class, 'store'])->name('lineups.store');
        Route::get('/lineups/{lineup}/dsp-pdf', [LineupController::class, 'downloadDsp'])->name('lineups.dsp.download');
        Route::delete('/lineups/{lineup}', [LineupController::class, 'destroy'])->name('lineups.destroy');

        Route::get('/transfers', [TransferController::class, 'index'])->name('transfers.index');
        Route::post('/transfers', [TransferController::class, 'store'])->name('transfers.store');
    });
});

require __DIR__.'/auth.php';

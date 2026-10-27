# 📦 Daftar Modul - SAPHYR (Football Future)

Proyek ini adalah sistem manajemen turnamen sepak bola berbasis web menggunakan Laravel 12, PHP 8.2, Blade, Tailwind CSS, Alpine.js, dan Vite. Berikut adalah daftar lengkap modul yang ada dalam proyek ini.

## 🗂️ 1. Modul Inti (Core Application)

| File/Direktori | Deskripsi |
|---|---|
| `bootstrap/app.php` | Bootstrapping aplikasi Laravel |
| `bootstrap/providers.php` | Registrasi service provider |
| `app/Providers/AppServiceProvider.php` | Service provider utama (boot & register) |
| `config/` | Konfigurasi aplikasi (app, auth, database, cache, logging, mail, queue, services, session, filesystem) |
| `routes/web.php` | Routing utama (web) |
| `routes/auth.php` | Routing autentikasi |
| `routes/console.php` | Perintah artisan kustom |
| `app/Http/Controller.php` | Base controller |

---

## 🔐 2. Modul Autentikasi & Otorisasi

**Lokasi:** `app/Http/Controllers/Auth/`, `app/Http/Middleware/`, `app/Http/Requests/Auth/`, `resources/views/auth/`

### Controller
- `AuthenticatedSessionController.php` - Login / logout
- `RegisteredUserController.php` - Registrasi user baru
- `PasswordController.php` - Penggantian password
- `PasswordResetLinkController.php` - Link reset password
- `NewPasswordController.php` - Form password baru
- `VerifyEmailController.php` - Verifikasi email
- `EmailVerificationNotificationController.php` - Kirim ulang notifikasi verifikasi
- `EmailVerificationPromptController.php` - Halaman notice verifikasi
- `ConfirmablePasswordController.php` - Konfirmasi password

### Middleware
- `RoleMiddleware.php` - Middleware berbasis role (super-admin, admin, club-admin)

### Request Validation
- `LoginRequest.php` - Validasi login

### Views
- `login.blade.php`, `register.blade.php`, `forgot-password.blade.php`, `reset-password.blade.php`, `verify-email.blade.php`, `confirm-password.blade.php`

---

## 👥 3. Modul User & Role Management

**Model:** `app/Models/User.php`, `app/Models/Role.php`

### Super Admin (Pengelola Sistem)
**Controller:** `app/Http/Controllers/SuperAdmin/AdminManagementController.php`

| Route | Aksi |
|---|---|
| `super-admin/admins` | Index / daftar admin |
| `super-admin/admins/create` | Form buat admin |
| `super-admin/admins` | Store admin |
| `super-admin/admins/{user}/edit` | Edit admin |
| `super-admin/admins/{user}` | Update / destroy admin |

### Club Admin Management
**Controller:** `app/Http/Controllers/SuperAdmin/ClubAdminManagementController.php`

| Route | Aksi |
|---|---|
| `super-admin/club-admins` | Index / daftar club admin |
| `super-admin/club-admins/create` | Form buat club admin |
| `super-admin/club-admins` | Store club admin |
| `super-admin/club-admins/{user}/edit` | Edit club admin |
| `super-admin/club-admins/{user}` | Update / destroy club admin |

### Views
- `resources/views/super-admin/admins/` (index, create, edit)
- `resources/views/super-admin/club-admins/` (index, create, edit)
- `resources/views/dashboard/super-admin.blade.php`

---

## 🏟️ 4. Modul Club Management

**Model:** `app/Models/Club.php`  
**Controller:** `app/Http/Controllers/SuperAdmin/ClubManagementController.php`

| Route | Aksi |
|---|---|
| `super-admin/clubs` | Index club |
| `super-admin/clubs` | Store club |
| `super-admin/clubs/{club}/logo` | Upload logo klub |
| `super-admin/clubs/{club}` | Update / destroy club |
| `super-admin/clubs/{club}/toggle` | Toggle aktif/nonaktif |

### Club Color (Warna Seragam)
**Controller:** `app/Http/Controllers/ClubAdmin/ClubColorController.php`

| Route | Aksi |
|---|---|
| `club-admin/club/colors` | Edit / update warna seragam |

### Views
- `resources/views/super-admin/clubs/index.blade.php`
- `resources/views/club-admin/club/colors.blade.php`

---

## 🏟️ 5. Modul Stadium Management

**Model:** `app/Models/Stadium.php`  
**Controller:** `app/Http/Controllers/SuperAdmin/StadiumController.php`

| Route | Aksi |
|---|---|
| `super-admin/stadiums` | Index stadion |
| `super-admin/stadiums/create` | Form buat stadion |
| `super-admin/stadiums` | Store stadion |
| `super-admin/stadiums/{stadium}/edit` | Edit stadion |
| `super-admin/stadiums/{stadium}` | Update / destroy stadion |
| `super-admin/stadiums/{stadium}/toggle` | Toggle aktif/nonaktif |

### Views
- `resources/views/super-admin/stadiums/` (index, create, edit)

---

## 👨‍⚖️ 6. Modul Referee Management (Wasit)

**Model:** `app/Models/Referee.php`  
**Controller:** `app/Http/Controllers/SuperAdmin/RefereeController.php`

| Route | Aksi |
|---|---|
| `super-admin/referees` | Index wasit |
| `super-admin/referees/create` | Form buat wasit |
| `super-admin/referees` | Store wasit |
| `super-admin/referees/{referee}` | Show / update / destroy wasit |

### Views
- `resources/views/super-admin/referees/` (index, create, show)

---

## 🏆 7. Modul Event / Tournament Management

**Model:** `app/Models/Event.php`, `app/Models/EventRegistration.php`, `app/Models/EventParticipant.php`, `app/Models/EventBracketRound.php`, `app/Models/EventBracketMatch.php`

### Event (Admin)
**Controller:** `app/Http/Controllers/Admin/EventController.php`

| Route | Aksi |
|---|---|
| `admin/events` | Index event |
| `admin/events/create` | Form buat event |
| `admin/events` | Store event |
| `admin/events/{event}/edit` | Edit event |
| `admin/events/{event}` | Update / destroy / toggle event |

### Event Participants (Admin)
**Controller:** `app/Http/Controllers/Admin/EventParticipantController.php`

| Route | Aksi |
|---|---|
| `admin/events/{event}/teams` | Index peserta event |
| `admin/events/{event}/participants` | Store peserta |
| `admin/events/{event}/bracket` | Generate bracket |
| `admin/events/{event}/bracket/slots` | Update slot bracket |
| `admin/events/{event}/bracket/matches/{match}` | Record pemenang bracket |
| `admin/events/{event}/bracket/matches/{match}/schedule` | Jadwalkan match dari bracket |
| `admin/events/{event}/bracket` | Destroy bracket |

### Event Registration (Club Admin)
**Controller:** `app/Http/Controllers/ClubAdmin/EventRegistrationController.php`

| Route | Aksi |
|---|---|
| `club-admin/events` | Index event |
| `club-admin/events/registration` | Register event |

### Request Validation (Admin)
- `StoreEventRequest.php`
- `StoreEventParticipantsRequest.php`
- `GenerateEventBracketRequest.php`
- `RecordBracketWinnerRequest.php`
- `UpdateBracketSlotsRequest.php`

### Views
- `resources/views/admin/events/` (index, create, edit, teams, partials/bracket-match.blade.php)
- `resources/views/club-admin/events/index.blade.php`
- `resources/views/public/events/` (index, show)

---

## ⚽ 8. Modul Match Management

**Model:** `app/Models/MatchModel.php`, `app/Models/MatchResult.php`, `app/Models/MatchGoal.php`, `app/Models/MatchCard.php`, `app/Models/MatchSubstitution.php`, `app/Models/MatchPlayerStat.php`, `app/Models/Lineup.php`, `app/Models/LineupPlayer.php`

**Controller:** `app/Http/Controllers/Admin/MatchController.php`

| Route | Aksi |
|---|---|
| `admin/matches` | Index match |
| `admin/matches` | Store match |
| `admin/matches/{match}` | Show / update / destroy match |
| `admin/matches/{match}/dsp-pdf` | Download PDF DSP |
| `admin/matches/{match}/summary-pdf` | Download PDF summary |
| `admin/matches/{match}/summary` | Update summary |
| `admin/matches/{match}/goals` | Store goal |
| `admin/matches/{match}/goals/{goal}` | Destroy goal |
| `admin/matches/{match}/cards` | Store card |
| `admin/matches/{match}/cards/{card}` | Destroy card |
| `admin/matches/{match}/substitutions` | Store substitution |
| `admin/matches/{match}/substitutions/{substitution}` | Destroy substitution |
| `admin/matches/{event}/bracket/matches/{match}/schedule` | Create from bracket |
| `admin/monitoring` | Monitoring match |

### Request Validation (Admin)
- `StoreMatchRequest.php`
- `StoreMatchGoalRequest.php`
- `StoreMatchCardRequest.php`
- `StoreMatchSubstitutionRequest.php`

### Views
- `resources/views/admin/matches/` (index, create_from_bracket, show, dsp-pdf.blade.php, summary-pdf.blade.php)
- `resources/views/admin/monitoring/index.blade.php`

---

## 👥 9. Modul Lineup System

**Model:** `app/Models/Lineup.php`, `app/Models/LineupPlayer.php`  
**Controller:** `app/Http/Controllers/ClubAdmin/LineupController.php`

| Route | Aksi |
|---|---|
| `club-admin/lineups` | Index lineup |
| `club-admin/lineups` | Store lineup |
| `club-admin/lineups/{lineup}/dsp-pdf` | Download PDF DSP |
| `club-admin/lineups/{lineup}` | Destroy lineup |

### Views
- `resources/views/club-admin/lineups/` (index.blade.php, dsp-pdf.blade.php)

---

## 🎯 10. Modul Player Management

**Model:** `app/Models/Player.php`  
**Controller:** `app/Http/Controllers/ClubAdmin/PlayerController.php`

| Route | Aksi |
|---|---|
| `club-admin/players` | Index pemain |
| `club-admin/players` | Store pemain |
| `club-admin/players/{player}` | Update / destroy pemain |

### Player Verification (Admin)
**Controller:** `app/Http/Controllers/Admin/PlayerVerificationController.php`

| Route | Aksi |
|---|---|
| `admin/players/verification` | Index verifikasi pemain |
| `admin/players/{player}/documents/{field}` | Preview dokumen |
| `admin/players/{player}/verification` | Update verifikasi |

### Request Validation
- `StorePlayerRequest.php` (ClubAdmin)
- `VerifyPlayerRequest.php` (Admin)

### Views
- `resources/views/club-admin/players/index.blade.php`
- `resources/views/admin/verification/index.blade.php`

---

## 🔄 11. Modul Transfer System

**Model:** `app/Models/Transfer.php`  
**Controller (Club Admin):** `app/Http/Controllers/ClubAdmin/TransferController.php`

| Route | Aksi |
|---|---|
| `club-admin/transfers` | Index transfer |
| `club-admin/transfers` | Store transfer |

### Transfer Verification (Super Admin)
**Controller:** `app/Http/Controllers/SuperAdmin/TransferVerificationController.php`

| Route | Aksi |
|---|---|
| `super-admin/transfers/verification` | Index verifikasi transfer |
| `super-admin/transfers/{transfer}/preview` | Preview transfer |
| `super-admin/transfers/{transfer}/verification` | Update verifikasi |

### Request Validation
- `StoreTransferRequest.php` (ClubAdmin)
- `VerifyTransferRequest.php` (SuperAdmin)

### Views
- `resources/views/club-admin/transfers/index.blade.php`
- `resources/views/super-admin/transfers/verification/index.blade.php`

---

## 👮 12. Modul Official Management

**Model:** `app/Models/Official.php`  
**Controller (Club Admin):** `app/Http/Controllers/ClubAdmin/OfficialController.php`

| Route | Aksi |
|---|---|
| `club-admin/officials` | Index official |
| `club-admin/officials` | Store official |
| `club-admin/officials/{official}` | Update / destroy official |

### Official Verification (Admin)
**Controller:** `app/Http/Controllers/Admin/OfficialVerificationController.php`

| Route | Aksi |
|---|---|
| `admin/officials/verification` | Index verifikasi official |
| `admin/officials/{official}/verification` | Update verifikasi |

### Request Validation
- `StoreOfficialRequest.php` (ClubAdmin)
- `VerifyOfficialRequest.php` (Admin)

### Views
- `resources/views/club-admin/officials/index.blade.php`
- `resources/views/admin/officials/verification/index.blade.php`

---

## ⚙️ 13. Modul Competition Settings

**Model:** `app/Models/CompetitionSetting.php`  
**Controller:** `app/Http/Controllers/SuperAdmin/CompetitionSettingController.php`

| Route | Aksi |
|---|---|
| `super-admin/settings` | Index pengaturan |
| `super-admin/settings/{match}/deadline` | Edit deadline match |
| `super-admin/settings/{match}/deadline` | Update deadline match |

### Request Validation
- `UpdateCompetitionSettingRequest.php`

### Views
- `resources/views/super-admin/settings/` (index, edit)

---

## 📄 14. Modul PDF Generation (DSP)

Digunakan untuk mencetak dokumen DSP (Daftar Sisa Pemain / lineup) dan summary pertandingan.

### Views PDF
- `resources/views/admin/matches/dsp-pdf.blade.php` - PDF DSP match (admin)
- `resources/views/admin/matches/summary-pdf.blade.php` - PDF summary match
- `resources/views/club-admin/lineups/dsp-pdf.blade.php` - PDF DSP lineup (club admin)
- `resources/views/partials/pdf/dsp-styles.blade.php` - Styling PDF DSP
- `resources/views/partials/pdf/dsp-jersey-colors.blade.php` - Warna jersey PDF

---

## 🌐 15. Modul Public Pages (Frontend Publik)

**Controllers:**
- `PublicHomeController.php`
- `PublicEventController.php`
- `PublicTeamController.php`
- `PublicStandingsController.php`

| Route | Aksi |
|---|---|
| `/` | Home |
| `/events` | Daftar event |
| `/events/{event}` | Detail event |
| `/teams` | Daftar klub |
| `/standings` | Klasemen |

### Views
- `resources/views/public/events/` (index, show)
- `resources/views/public/teams/index.blade.php`
- `resources/views/public/standings/index.blade.php`
- `resources/views/home.blade.php`
- `resources/views/partials/public/` (nav, footer)

---

## 📊 16. Modul Dashboard (Per Role)

| File | Role |
|---|---|
| `resources/views/dashboard/admin.blade.php` | Admin |
| `resources/views/dashboard/club-admin.blade.php` | Club Admin |
| `resources/views/dashboard/super-admin.blade.php` | Super Admin |

**Controller:** `DashboardController.php`

---

## 👤 17. Modul Profile Management

**Controller:** `ProfileController.php`  
**Request:** `ProfileUpdateRequest.php`

| Route | Aksi |
|---|---|
| `/profile` | Edit profile |
| `/profile` | Update profile |
| `/profile` | Destroy (hapus akun) |

### Views
- `resources/views/profile/edit.blade.php`
- `resources/views/profile/partials/` (delete-user-form, update-password-form, update-profile-information-form)

---

## 🏗️ 18. Modul Club Completeness (Monitoring Admin)

**Controller:** `app/Http/Controllers/Admin/ClubCompletenessController.php`

| Route | Aksi |
|---|---|
| `admin/club-completeness` | Index kelengkapan klub |
| `admin/club-completeness/{club}` | Show detail klub |
| `admin/club-completeness/{club}/players/{player}` | Show / destroy pemain klub |
| `admin/club-completeness/{club}/officials/{official}` | Show / destroy official klub |

### Views
- `resources/views/admin/club-completeness/` (index, show, player-show, official-show)

---

## 🧩 19. Service Layer (Business Logic)

**Lokasi:** `app/Services/`

| File | Deskripsi |
|---|---|
| `MatchService.php` | Logika bisnis pertandingan |
| `PlayerService.php` | Logika bisnis pemain |
| `TransferService.php` | Logika bisnis transfer |
| `PlayerDisciplineService.php` | Logika disiplin pemain (kartu) |
| `CompetitionSettingService.php` | Logika pengaturan kompetisi |
| `BracketService.php` | Logika generate bracket knockout |

---

## 🗄️ 20. Repository Layer (Data Access)

**Lokasi:** `app/Repositories/`

| File | Deskripsi |
|---|---|
| `MatchRepository.php` | Akses data match |
| `PlayerRepository.php` | Akses data pemain |
| `TransferRepository.php` | Akses data transfer |

---

## 📐 21. Model Layer (Eloquent)

**Lokasi:** `app/Models/`

| File | Deskripsi |
|---|---|
| `User.php` | Pengguna sistem |
| `Role.php` | Role pengguna |
| `Club.php` | Klub / tim sepak bola |
| `Stadium.php` | Stadion |
| `Referee.php` | Wasit pertandingan |
| `Official.php` | Official klub |
| `Player.php` | Pemain |
| `Event.php` | Event / turnamen |
| `EventRegistration.php` | Pendaftaran pemain ke event |
| `EventParticipant.php` | Peserta event |
| `EventBracketRound.php` | Ronde bracket |
| `EventBracketMatch.php` | Match bracket |
| `MatchModel.php` | Pertandingan |
| `MatchResult.php` | Hasil pertandingan |
| `Lineup.php` | Komposisi lineup |
| `LineupPlayer.php` | Pemain dalam lineup |
| `MatchGoal.php` | Gol pertandingan |
| `MatchCard.php` | Kartu (kuning/merah) |
| `MatchSubstitution.php` | Substitusi |
| `MatchPlayerStat.php` | Statistik pemain per match |
| `CompetitionSetting.php` | Pengaturan kompetisi |
| `Transfer.php` | Transfer pemain |

---

## 🧩 22. View Components

**Lokasi:** `app/View/Components/`

| File | Deskripsi |
|---|---|
| `AppLayout.php` | Layout untuk user terautentikasi |
| `GuestLayout.php` | Layout untuk guest |

### Layouts Blade
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/guest.blade.php`
- `resources/views/layouts/navigation.blade.php`

---

## 🗃️ 23. Database Layer

### Migrations (`database/migrations/`)
28 file migrasi, antara lain:
- `create_users_table`
- `create_competition_core_tables` (clubs, events, matches, players, dll.)
- `create_match_goals_table`
- `create_match_cards_table`
- `create_match_substitutions_table`
- `create_officials_table`
- `create_event_participants_and_bracket_tables`
- dan berbagai penyesuaian schema lainnya

### Seeders (`database/seeders/`)
| File | Deskripsi |
|---|---|
| `DatabaseSeeder.php` | Seeder utama |
| `UserSeeder.php` | Seed data user |
| `RoleSeeder.php` | Seed data role |
| `ClubSeeder.php` | Seed data klub |
| `PlayerSeeder.php` | Seed data pemain |
| `CompetitionDataSeeder.php` | Seed data kompetisi |

### Factories (`database/factories/`)
- `UserFactory.php`

---

## 🛣️ 24. Ringkasan Route Groups

| Prefix | Middleware | Role | Deskripsi |
|---|---|---|---|
| (root) | guest | - | Halaman publik (home, events, teams, standings) |
| (root) | auth | semua user terautentikasi | Dashboard & profile |
| `super-admin` | role:super-admin | Super Admin | Manajemen admin, club admin, club, stadium, referee, transfer verification, settings, impersonation |
| `admin` | role:admin,super-admin | Admin / Super Admin | Event, match, monitoring, player verification, official verification, club completeness |
| `club-admin` | role:club-admin | Club Admin | Pemain, official, warna klub, registrasi event, lineup, transfer |
| auth routes | guest / auth | - | Login, register, lupa password, verifikasi email |

---

## 📦 25. Teknologi yang Digunakan

| Layer | Teknologi |
|---|---|
| Backend Framework | Laravel 12 |
| PHP | ^8.2 |
| Template Engine | Blade |
| CSS Framework | Tailwind CSS 3.1.0 |
| JavaScript | Alpine.js 3.4.2 |
| HTTP Client | Axios 1.11.0 |
| Build Tool | Vite 7.0.7 |
| PDF Generator | DomPDF 3.1 |
| Database | SQLite / MySQL |
| Testing | PHPUnit 11.5.50 |
| Code Style | Laravel Pint 1.24 |

---

**Dokumen ini dihasilkan dari pemindaian struktur proyek `football_future` (SAPHYR).**

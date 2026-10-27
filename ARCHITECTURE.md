# 🏗️ Arsitektur Teknologi - SAPHYR

## 📋 Daftar Isi

1. [Overview](#overview)
2. [Technology Stack](#technology-stack)
3. [Arsitektur Sistem](#arsitektur-sistem)
4. [Struktur Folder](#struktur-folder)
5. [Flow Data](#flow-data)
6. [Fitur Utama](#fitur-utama)
7. [Development Workflow](#development-workflow)

---

## Overview

**SAPHYR** adalah aplikasi manajemen turnamen sepak bola berbasis web yang dibangun dengan teknologi modern dan arsitektur berlapis (layered architecture). Aplikasi ini menggabungkan backend yang kuat dengan frontend yang responsif untuk memberikan pengalaman pengguna yang optimal.

---

## Technology Stack

| Layer                  | Teknologi          | Versi        | Fungsi                                 |
| ---------------------- | ------------------ | ------------ | -------------------------------------- |
| **Framework Backend**  | Laravel            | 12.0         | Web Framework, Routing, Authentication |
| **Runtime**            | PHP                | ^8.2         | Server-side Programming Language       |
| **Build Tool**         | Vite               | 7.0.7        | Module Bundler, Hot Module Reload      |
| **Frontend Framework** | Blade              | Built-in     | Template Engine                        |
| **Interactivity**      | Alpine.js          | 3.4.2        | Lightweight JavaScript Framework       |
| **Styling**            | Tailwind CSS       | 3.1.0        | Utility-first CSS Framework            |
| **CSS Plugin**         | @tailwindcss/forms | 0.5.2        | Form Styling Components                |
| **CSS Vite Plugin**    | @tailwindcss/vite  | 4.0.0        | Vite Integration                       |
| **HTTP Client**        | Axios              | 1.11.0       | AJAX Requests                          |
| **CSS Preprocessor**   | PostCSS            | 8.4.31       | CSS Transformations                    |
| **Autoprefixer**       | autoprefixer       | 10.4.2       | Browser Compatibility                  |
| **ORM**                | Eloquent           | Built-in     | Object-Relational Mapping              |
| **Database**           | SQLite/MySQL       | Configurable | Data Storage                           |
| **PDF Generation**     | DomPDF             | 3.1          | Generate PDF Documents                 |
| **Testing**            | PHPUnit            | 11.5.50      | Unit & Feature Testing                 |
| **Code Quality**       | Laravel Pint       | 1.24         | PHP Code Style Fixer                   |
| **Task Runner**        | Concurrently       | 9.0.1        | Run Multiple Commands                  |
| **Dev Dependencies**   | Laravel Breeze     | 2.4          | Authentication Scaffolding             |
| **Logging**            | Laravel Pail       | 1.2.2        | Real-time Log Viewer                   |
| **Mock Testing**       | Mockery            | 1.6          | Object Mocking Library                 |

---

## Arsitektur Sistem

### Diagram Alur Data

```
┌─────────────────────────────────────────────────────────────────┐
│                      CLIENT (BROWSER)                            │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ - Blade Templates (HTML)                                  │  │
│  │ - Alpine.js (Interactive Components)                     │  │
│  │ - Tailwind CSS (Styling)                                 │  │
│  │ - Axios (HTTP Requests)                                  │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────┬──────────────────────────────────────┘
                          │ HTTP Request/Response
                          ▼
┌─────────────────────────────────────────────────────────────────┐
│                   LARAVEL APPLICATION                            │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ Routing Layer                                             │  │
│  │ - web.php (Web Routes)                                   │  │
│  │ - auth.php (Authentication Routes)                       │  │
│  └──────────────────────────────────────────────────────────┘  │
│                          ▼                                       │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ Middleware Layer                                          │  │
│  │ - Authentication, Authorization                          │  │
│  │ - Request Validation                                     │  │
│  └──────────────────────────────────────────────────────────┘  │
│                          ▼                                       │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ Controller Layer                                          │  │
│  │ - Handle Requests                                        │  │
│  │ - Orchestrate Business Logic                             │  │
│  └──────────────────────────────────────────────────────────┘  │
│                          ▼                                       │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ Service Layer                                             │  │
│  │ - MatchService          - PlayerService                  │  │
│  │ - TransferService       - PlayerDisciplineService        │  │
│  │ - CompetitionSettingService                              │  │
│  └──────────────────────────────────────────────────────────┘  │
│                          ▼                                       │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ Repository Layer                                          │  │
│  │ - MatchRepository       - PlayerRepository               │  │
│  │ - TransferRepository                                      │  │
│  └──────────────────────────────────────────────────────────┘  │
│                          ▼                                       │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ Eloquent ORM Layer                                        │  │
│  │ - Model Mapping                                           │  │
│  │ - Relationship Management                                │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────┬──────────────────────────────────────┘
                          │
        ┌─────────────────┼─────────────────┐
        ▼                 ▼                 ▼
┌───────────────┐ ┌───────────────┐ ┌───────────────┐
│   DATABASE    │ │   FILE SYSTEM │ │   QUEUE       │
│               │ │               │ │   SYSTEM      │
│ SQLite/MySQL  │ │ Storage/Cache │ │ Background    │
│               │ │               │ │ Jobs          │
└───────────────┘ └───────────────┘ └───────────────┘
```

### Layered Architecture

```
┌──────────────────────────────────────────┐
│         PRESENTATION LAYER                │
│  (Blade Templates + Alpine.js + CSS)     │
└──────────────────────────────────────────┘
                    ▼
┌──────────────────────────────────────────┐
│      CONTROLLER LAYER                     │
│  (Request Handling, Response)            │
└──────────────────────────────────────────┘
                    ▼
┌──────────────────────────────────────────┐
│       SERVICE LAYER                       │
│  (Business Logic, Validation)            │
└──────────────────────────────────────────┘
                    ▼
┌──────────────────────────────────────────┐
│      REPOSITORY LAYER                     │
│  (Data Access Abstraction)               │
└──────────────────────────────────────────┘
                    ▼
┌──────────────────────────────────────────┐
│         MODEL LAYER (Eloquent)            │
│  (ORM, Database Relationships)           │
└──────────────────────────────────────────┘
                    ▼
┌──────────────────────────────────────────┐
│        DATABASE LAYER                     │
│  (SQLite/MySQL, Tables, Migrations)      │
└──────────────────────────────────────────┘
```

---

## Database Design

### Entity Relationship Diagram (ERD)

```
                          ┌─────────────┐
                          │   USERS     │
                          └─────────────┘
                                 │
                    ┌────────────┼────────────┐
                    │            │            │
              ┌─────────┐   ┌─────────┐ ┌──────────┐
              │ EVENTS  │   │ CLUBS   │ │OFFICIALS │
              └─────────┘   └─────────┘ └──────────┘
                    │            │            │
         ┌──────────┼──────┐     │     ┌──────┴──────┐
         │          │      │     │     │             │
    ┌────────┐ ┌─────────────┐  │  ┌─────────┐ ┌─────────┐
    │STADIUMS│ │REFEREES     │  │  │ROLES    │ │ROLE_USER│
    └────────┘ └─────────────┘  │  └─────────┘ └─────────┘
                    │            │
              ┌─────┴─────┐      │
              │           │      │
         ┌───────────┐ ┌──────────────┐
         │ MATCHES   │ │   PLAYERS    │
         └───────────┘ └──────────────┘
              │               │
    ┌─────────┼───────────────┼──────────────┐
    │         │               │              │
┌────────┐ ┌──────────┐ ┌─────────────────┐ │
│LINEUPS │ │MATCH_    │ │EVENT_           │ │
│        │ │RESULTS   │ │REGISTRATIONS    │ │
└────────┘ └──────────┘ └─────────────────┘ │
    │
┌────────────┐
│LINEUP_     │
│PLAYERS     │
└────────────┘

┌──────────────┐ ┌──────────────┐ ┌─────────────────┐
│MATCH_GOALS   │ │MATCH_CARDS   │ │MATCH_           │
│              │ │              │ │SUBSTITUTIONS    │
└──────────────┘ └──────────────┘ └─────────────────┘

┌──────────────┐ ┌──────────────────┐ ┌──────────────┐
│TRANSFERS     │ │MATCH_PLAYER_STATS│ │COMPETITION_  │
│              │ │                  │ │SETTINGS      │
└──────────────┘ └──────────────────┘ └──────────────┘
```

### Core Tables

#### **users**

Tabel untuk menyimpan informasi pengguna sistem.

| Column              | Type      | Description        |
| ------------------- | --------- | ------------------ |
| `id`                | bigint    | Primary Key        |
| `name`              | string    | Nama pengguna      |
| `email`             | string    | Email (unique)     |
| `email_verified_at` | timestamp | Email verification |
| `password`          | string    | Password (hashed)  |
| `remember_token`    | string    | Remember me token  |
| `created_at`        | timestamp | Created timestamp  |
| `updated_at`        | timestamp | Updated timestamp  |

#### **clubs** (Teams/Clubs)

Tabel untuk menyimpan informasi klub/tim.

| Column        | Type      | Description         |
| ------------- | --------- | ------------------- |
| `id`          | bigint    | Primary Key         |
| `name`        | string    | Nama klub           |
| `logo_path`   | string    | Path ke logo klub   |
| `shirt_color` | string    | Warna seragam utama |
| `pants_color` | string    | Warna celana        |
| `socks_color` | string    | Warna kaus kaki     |
| `created_at`  | timestamp | Created timestamp   |
| `updated_at`  | timestamp | Updated timestamp   |

#### **stadiums**

Tabel untuk menyimpan informasi stadion.

| Column       | Type      | Description       |
| ------------ | --------- | ----------------- |
| `id`         | bigint    | Primary Key       |
| `name`       | string    | Nama stadion      |
| `city`       | string    | Kota stadion      |
| `address`    | string    | Alamat lengkap    |
| `is_active`  | boolean   | Status aktif      |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

#### **referees**

Tabel untuk menyimpan data wasit pertandingan.

| Column           | Type      | Description       |
| ---------------- | --------- | ----------------- |
| `id`             | bigint    | Primary Key       |
| `name`           | string    | Nama wasit        |
| `license_number` | string    | Nomor lisensi     |
| `phone`          | string    | Nomor telepon     |
| `created_at`     | timestamp | Created timestamp |
| `updated_at`     | timestamp | Updated timestamp |

#### **events**

Tabel untuk menyimpan informasi turnamen/kompetisi.

| Column                  | Type      | Description              |
| ----------------------- | --------- | ------------------------ |
| `id`                    | bigint    | Primary Key              |
| `name`                  | string    | Nama event/turnamen      |
| `competition_type`      | enum      | 'league' atau 'knockout' |
| `min_age`               | tinyint   | Usia minimum pemain      |
| `max_age`               | tinyint   | Usia maksimum pemain     |
| `start_date`            | date      | Tanggal mulai            |
| `end_date`              | date      | Tanggal berakhir         |
| `is_active`             | boolean   | Status aktif             |
| `card_discipline_rules` | json      | Aturan kartu disiplin    |
| `created_at`            | timestamp | Created timestamp        |
| `updated_at`            | timestamp | Updated timestamp        |

#### **players**

Tabel untuk menyimpan data pemain.

| Column              | Type      | Description                 |
| ------------------- | --------- | --------------------------- |
| `id`                | bigint    | Primary Key                 |
| `club_id`           | bigint    | FK ke clubs                 |
| `name`              | string    | Nama pemain                 |
| `nik`               | string    | Nomor Identitas (unique)    |
| `birth_date`        | date      | Tanggal lahir               |
| `birth_place`       | string    | Tempat lahir                |
| `jersey_number`     | smallint  | Nomor punggung              |
| `document_path`     | string    | Path dokumen pemain         |
| `status`            | enum      | pending, approved, rejected |
| `verification_note` | text      | Catatan verifikasi          |
| `created_at`        | timestamp | Created timestamp           |
| `updated_at`        | timestamp | Updated timestamp           |

**Unique Constraints:**

- `nik` (Nomor Identitas)
- `club_id + jersey_number` (Nomor punggung per klub)

#### **matches**

Tabel untuk menyimpan data pertandingan.

| Column            | Type      | Description                  |
| ----------------- | --------- | ---------------------------- |
| `id`              | bigint    | Primary Key                  |
| `event_id`        | bigint    | FK ke events                 |
| `home_club_id`    | bigint    | FK ke clubs (tim tuan rumah) |
| `away_club_id`    | bigint    | FK ke clubs (tim tamu)       |
| `stadium_id`      | bigint    | FK ke stadiums               |
| `referee_id`      | bigint    | FK ke referees               |
| `match_date`      | datetime  | Waktu pertandingan           |
| `summary`         | text      | Ringkasan pertandingan       |
| `supervisor_note` | text      | Catatan supervisor           |
| `lineup_deadline` | timestamp | Deadline pengumpulan lineup  |
| `created_at`      | timestamp | Created timestamp            |
| `updated_at`      | timestamp | Updated timestamp            |

#### **match_results**

Tabel untuk menyimpan hasil pertandingan.

| Column       | Type      | Description            |
| ------------ | --------- | ---------------------- |
| `id`         | bigint    | Primary Key            |
| `match_id`   | bigint    | FK ke matches (unique) |
| `home_score` | smallint  | Skor tim tuan rumah    |
| `away_score` | smallint  | Skor tim tamu          |
| `created_at` | timestamp | Created timestamp      |
| `updated_at` | timestamp | Updated timestamp      |

#### **lineups**

Tabel untuk menyimpan komposisi pemain dalam pertandingan.

| Column        | Type      | Description       |
| ------------- | --------- | ----------------- |
| `id`          | bigint    | Primary Key       |
| `match_id`    | bigint    | FK ke matches     |
| `club_id`     | bigint    | FK ke clubs       |
| `shirt_color` | string    | Warna seragam     |
| `pants_color` | string    | Warna celana      |
| `socks_color` | string    | Warna kaus kaki   |
| `created_at`  | timestamp | Created timestamp |
| `updated_at`  | timestamp | Updated timestamp |

**Unique Constraints:**

- `match_id + club_id`

#### **lineup_players**

Tabel untuk menyimpan pemain dalam lineup (many-to-many).

| Column           | Type      | Description                 |
| ---------------- | --------- | --------------------------- |
| `id`             | bigint    | Primary Key                 |
| `lineup_id`      | bigint    | FK ke lineups               |
| `player_id`      | bigint    | FK ke players               |
| `position_type`  | enum      | 'starter' atau 'substitute' |
| `field_position` | string    | Posisi di lapangan          |
| `shirt_number`   | smallint  | Nomor punggung saat bermain |
| `captain`        | boolean   | Apakah kapten               |
| `created_at`     | timestamp | Created timestamp           |
| `updated_at`     | timestamp | Updated timestamp           |

**Unique Constraints:**

- `lineup_id + player_id`

#### **match_goals**

Tabel untuk menyimpan data gol dalam pertandingan.

| Column         | Type      | Description              |
| -------------- | --------- | ------------------------ |
| `id`           | bigint    | Primary Key              |
| `match_id`     | bigint    | FK ke matches            |
| `player_id`    | bigint    | FK ke players            |
| `club_id`      | bigint    | FK ke clubs              |
| `minute_label` | string    | Menit gol (e.g., "45+2") |
| `is_penalty`   | boolean   | Apakah dari penalti      |
| `created_at`   | timestamp | Created timestamp        |
| `updated_at`   | timestamp | Updated timestamp        |

**Indexes:**

- `match_id, club_id`

#### **match_cards**

Tabel untuk menyimpan kartu kuning/merah.

| Column         | Type      | Description           |
| -------------- | --------- | --------------------- |
| `id`           | bigint    | Primary Key           |
| `match_id`     | bigint    | FK ke matches         |
| `player_id`    | bigint    | FK ke players         |
| `club_id`      | bigint    | FK ke clubs           |
| `minute_label` | string    | Menit kartu diberikan |
| `card_type`    | string    | 'yellow' atau 'red'   |
| `created_at`   | timestamp | Created timestamp     |
| `updated_at`   | timestamp | Updated timestamp     |

**Indexes:**

- `match_id, club_id`

#### **match_substitutions**

Tabel untuk menyimpan pergantian pemain.

| Column          | Type      | Description            |
| --------------- | --------- | ---------------------- |
| `id`            | bigint    | Primary Key            |
| `match_id`      | bigint    | FK ke matches          |
| `club_id`       | bigint    | FK ke clubs            |
| `minute_label`  | string    | Menit pergantian       |
| `player_out_id` | bigint    | FK ke players (keluar) |
| `player_in_id`  | bigint    | FK ke players (masuk)  |
| `created_at`    | timestamp | Created timestamp      |
| `updated_at`    | timestamp | Updated timestamp      |

**Indexes:**

- `match_id, club_id`

#### **match_player_stats**

Tabel untuk menyimpan statistik pemain per pertandingan.

| Column       | Type      | Description       |
| ------------ | --------- | ----------------- |
| `id`         | bigint    | Primary Key       |
| `match_id`   | bigint    | FK ke matches     |
| `player_id`  | bigint    | FK ke players     |
| `goals`      | smallint  | Jumlah gol        |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

**Unique Constraints:**

- `match_id + player_id`

#### **transfers**

Tabel untuk menyimpan history perpindahan pemain.

| Column              | Type      | Description                       |
| ------------------- | --------- | --------------------------------- |
| `id`                | bigint    | Primary Key                       |
| `player_id`         | bigint    | FK ke players                     |
| `from_club_id`      | bigint    | FK ke clubs (asal klub)           |
| `to_club_id`        | bigint    | FK ke clubs (klub tujuan)         |
| `status`            | enum      | available, non-available, blocked |
| `transfer_state`    | enum      | requested, released, accepted     |
| `document_path`     | string    | Path dokumen transfer             |
| `released_at`       | timestamp | Waktu pembebasan                  |
| `accepted_at`       | timestamp | Waktu penerimaan                  |
| `verified_at`       | timestamp | Waktu verifikasi                  |
| `verification_note` | text      | Catatan verifikasi                |
| `created_at`        | timestamp | Created timestamp                 |
| `updated_at`        | timestamp | Updated timestamp                 |

#### **event_registrations**

Tabel untuk menyimpan registrasi pemain dalam event (many-to-many).

| Column       | Type      | Description       |
| ------------ | --------- | ----------------- |
| `id`         | bigint    | Primary Key       |
| `event_id`   | bigint    | FK ke events      |
| `player_id`  | bigint    | FK ke players     |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

**Unique Constraints:**

- `event_id + player_id`

#### **competition_settings**

Tabel untuk menyimpan pengaturan kompetisi global.

| Column                 | Type      | Description                 |
| ---------------------- | --------- | --------------------------- |
| `id`                   | bigint    | Primary Key                 |
| `player_crud_deadline` | timestamp | Deadline CRUD pemain        |
| `lineup_deadline`      | timestamp | Deadline pengumpulan lineup |
| `created_at`           | timestamp | Created timestamp           |
| `updated_at`           | timestamp | Updated timestamp           |

### Database Relationships

```
events ──────┬──────────> matches
             │              │
             │              ├──> match_results
             │              ├──> lineups
             │              ├──> match_goals
             │              ├──> match_cards
             │              ├──> match_substitutions
             │              └──> match_player_stats
             │
             └──────────> event_registrations

clubs ───────┬──────────> players
             │              │
             │              ├──> event_registrations
             │              ├──> match_goals
             │              ├──> match_cards
             │              └──> transfers
             │
             ├──────────> lineups
             │
             └──────────> matches (home & away)

matches ─────┬──────────> lineups ─────────> lineup_players
             │
             ├──────────> match_goals
             ├──────────> match_cards
             ├──────────> match_substitutions
             └──────────> match_player_stats
```

### Database Constraints

**Foreign Keys:**

- All foreign keys use `CASCADE ON DELETE` for data integrity
- Some nullable FKs use `NULL ON DELETE` (stadium_id, referee_id, from_club_id)

**Unique Indexes:**

- `players.nik`
- `players.club_id + jersey_number`
- `matches.match_results.match_id`
- `lineups.match_id + club_id`
- `lineup_players.lineup_id + player_id`
- `event_registrations.event_id + player_id`
- `match_player_stats.match_id + player_id`

**Non-Unique Indexes:**

- `match_goals.match_id + club_id`
- `match_cards.match_id + club_id`
- `match_substitutions.match_id + club_id`

---

## Struktur Folder

### Backend Structure

```
app/
├── Http/
│   ├── Controllers/          ← Menangani HTTP Requests
│   ├── Middleware/           ← Request Processing
│   └── Requests/             ← Form Validation
│
├── Models/                   ← Eloquent Models
│   ├── Match.php             ← Match Management
│   ├── Player.php            ← Player Management
│   ├── Club.php              ← Club/Team Data
│   ├── Lineup.php            ← Lineup Komposisi
│   ├── Event.php             ← Tournament Events
│   ├── Transfer.php          ← Transfer History
│   ├── Official.php          ← Officials/Referees
│   ├── Stadium.php           ← Stadium Data
│   ├── MatchCard.php         ← Yellow/Red Cards
│   ├── MatchGoal.php         ← Goals Record
│   ├── MatchSubstitution.php ← Substitutions
│   ├── MatchPlayerStat.php   ← Player Statistics
│   └── ... (other models)
│
├── Services/                 ← Business Logic
│   ├── MatchService.php
│   ├── PlayerService.php
│   ├── TransferService.php
│   ├── PlayerDisciplineService.php
│   ├── CompetitionSettingService.php
│   └── ... (other services)
│
├── Repositories/             ← Data Access Abstraction
│   ├── MatchRepository.php
│   ├── PlayerRepository.php
│   ├── TransferRepository.php
│   └── ... (other repositories)
│
├── View/
│   └── Components/           ← Reusable View Components
│
└── Providers/
    └── AppServiceProvider.php ← Service Registration
```

### Frontend Structure

```
resources/
├── css/
│   └── app.css              ← Tailwind CSS (imported)
│
├── js/
│   └── app.js               ← Alpine.js & Axios configuration
│
└── views/
    ├── layouts/             ← Base Templates
    ├── public/              ← Public Pages
    │   ├── events/          ← Events Listing
    │   ├── teams/           ← Teams Directory
    │   └── standings/       ← Competition Standings
    ├── admin/               ← Admin Dashboard
    ├── club-admin/          ← Club Administrator Pages
    │   ├── lineups/         ← Lineup Management
    │   ├── players/         ← Player Management
    │   └── ... (other admin pages)
    └── auth/                ← Authentication Pages
```

### Database Structure

```
database/
├── migrations/              ← Schema Definitions
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 0001_01_01_000001_create_cache_table.php
│   ├── 2026_04_21_000100_create_competition_core_tables.php
│   ├── 2026_04_21_020000_add_logo_path_to_clubs_table.php
│   ├── 2026_04_23_120000_update_players_for_club_admin_input.php
│   └── ... (other migrations)
│
├── factories/               ← Test Data Generation
│   └── UserFactory.php
│
└── seeders/                 ← Initial Data
    └── DatabaseSeeder.php
```

### Configuration Structure

```
config/
├── app.php                  ← Application Configuration
├── auth.php                 ← Authentication Settings
├── cache.php                ← Cache Configuration
├── database.php             ← Database Connections
├── filesystems.php          ← File Storage
├── logging.php              ← Logging Configuration
├── mail.php                 ← Mail Settings
├── queue.php                ← Queue Configuration
├── services.php             ← Third-party Services
└── session.php              ← Session Configuration
```

---

## Flow Data

### Alur Request-Response Umum

```
User Browser
    ↓ (1. User Input / Navigation)
    ↓
Routes (web.php / auth.php)
    ↓ (2. Route Matching)
    ↓
Middleware Stack
    ↓ (3. Authentication, Authorization, CSRF)
    ↓
Controller Action
    ↓ (4. Request Processing)
    ↓
Service Layer
    ↓ (5. Business Logic Execution)
    ↓
Repository Layer
    ↓ (6. Data Access)
    ↓
Eloquent Models
    ↓ (7. Query Building)
    ↓
Database
    ↓ (8. Execute Query)
    ↓
Response Data Back
    ↓
View / JSON Response
    ↓ (9. Render Response)
    ↓
Browser Display
```

### Alur Match Reporting

```
Club Admin
    ↓
Reports Match Score/Details
    ↓
MatchController@store
    ↓
MatchService->createOrUpdateMatch()
    ↓
MatchRepository->save()
    ↓
MatchModel + Related Models (Goal, Cards, Substitutions)
    ↓
Database Update
    ↓
Update Standings/Rankings
    ↓
Email/Notification (if enabled)
    ↓
Public Display Updated
```

---

## Fitur Utama

### 1. **Match Management** ⚽

- Pembuatan pertandingan
- Pencatatan hasil skor
- Recording gol, kartu, substitusi
- Statistik pemain per pertandingan

### 2. **Lineup System** 👥

- Komposisi pemain per pertandingan
- Warna seragam (shirt, pants, socks)
- DSP PDF Export
- Perubahan formasi

### 3. **Player Management** 🎯

- Database pemain
- Statistik kinerja
- Transfer history
- Disiplin (kartu kuning/merah)

### 4. **Competition Settings** 🏆

- Konfigurasi turnamen
- Aturan poin
- Grup/babak
- Jadwal pertandingan

### 5. **Public-Facing Pages** 🌐

- **Events Page**: Daftar semua turnamen
- **Teams Page**: Direktori klub peserta
- **Standings Page**: Klasemen dan top scorers
- Filter dan pencarian

### 6. **PDF Generation** 📄

- Export lineup sebagai DSP
- Reporting documents
- Match records

### 7. **Background Jobs** 🔄

- Queue system untuk tugas berat
- Email notifications
- Data synchronization

---

## Development Workflow

### Setup Project

```bash
# 1. Install Dependencies
composer install
npm install

# 2. Environment Configuration
cp .env.example .env
php artisan key:generate

# 3. Database Setup
php artisan migrate
php artisan db:seed

# 4. Build Frontend Assets
npm run build
```

### Development Mode

```bash
# Run all dev servers concurrently
npm run dev

# Or manually:
php artisan serve          # Start Laravel dev server
npm run dev                # Start Vite with HMR
php artisan queue:listen   # Process background jobs
php artisan pail          # Monitor logs in real-time
```

### Build for Production

```bash
npm run build              # Build frontend assets
php artisan config:cache   # Cache configuration
php artisan route:cache    # Cache routes
php artisan view:cache     # Cache views
```

### Testing

```bash
composer test              # Run all tests
php artisan test           # Run feature & unit tests
./vendor/bin/phpunit       # Direct PHPUnit execution
```

---

## Key Dependencies

### Backend Dependencies

- **laravel/framework**: Core Laravel framework
- **laravel/tinker**: Interactive REPL
- **barryvdh/laravel-dompdf**: PDF generation

### Frontend Dependencies

- **vite**: Modern build tool
- **tailwindcss**: Utility-first CSS
- **alpinejs**: Alpine JavaScript framework
- **axios**: HTTP client

### Dev Dependencies

- **phpunit/phpunit**: Testing framework
- **laravel/pint**: Code style fixer
- **mockery/mockery**: Mock objects
- **fakerphp/faker**: Fake data generation

---

## Performance Considerations

### Database

- Use eager loading untuk Eloquent relationships
- Index frequently queried columns
- Cache queries dengan Redis jika needed

### Frontend

- Vite hot module replacement untuk development
- Tailwind CSS purging untuk production
- Alpine.js untuk interactivity ringan

### Backend

- Queue system untuk heavy tasks
- Caching layer untuk repeated queries
- Pagination untuk large datasets

---

## Security Features

- ✅ CSRF Protection (middleware)
- ✅ Authentication & Authorization
- ✅ SQL Injection Prevention (Eloquent)
- ✅ XSS Protection (Blade escaping)
- ✅ Form Validation (Request classes)
- ✅ Password Hashing (bcrypt)

---

## Monitoring & Logging

- **Laravel Pail**: Real-time log viewer
- **Log Files**: `storage/logs/`
- **Database Logging**: Configured in `config/logging.php`
- **Queue Monitoring**: `php artisan queue:listen`

---

## Deployment Considerations

1. **Environment Variables**: Set in `.env`
2. **Database Migrations**: Run `php artisan migrate`
3. **Asset Build**: Run `npm run build`
4. **Cache**: Run `php artisan config:cache`
5. **Storage**: Ensure write permissions
6. **Queue Worker**: Run `php artisan queue:work` (production)

---

**Last Updated**: 2026-06-11  
**Project**: SAPHYR Management System

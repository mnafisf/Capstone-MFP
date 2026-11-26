<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Match Summary Report</title>

    <style>
        @page { margin: 24px 28px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #202938;
            font-size: 9px;
            line-height: 1.4;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d7dde5; padding: 5px 6px; vertical-align: middle; }
        th { background: #edf1f5; color: #263445; font-weight: bold; text-align: center; }
        .header-table, .header-table td,
        .team-score-table, .team-score-table td,
        .signature-table, .signature-table td { border: none; }
        .header-table { margin-bottom: 12px; }
        .logo { width: 58px; height: auto; }
        .title { text-align: center; }
        .title h1 { margin: 0; font-size: 21px; letter-spacing: 1.2px; color: #172b4d; }
        .title h2 { margin: 4px 0 0; font-size: 11px; color: #526174; }
        .report-label { font-size: 8px; color: #718096; text-align: right; }
        .match-info-table { margin: 8px 0 12px; }
        .match-info-table td { text-align: center; }
        .team-score-table { margin: 5px 0 14px; }
        .team-name { font-size: 12px; font-weight: bold; text-align: center; color: #172b4d; }
        .score { font-size: 27px; font-weight: bold; text-align: center; color: #172b4d; white-space: nowrap; }
        .score-caption { text-align: center; font-size: 7px; color: #718096; }
        .section-title {
            margin: 12px 0 5px;
            padding: 6px 8px;
            background: #e9eef5;
            border-left: 4px solid #263f63;
            color: #172b4d;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: .4px;
        }
        .player-columns { table-layout: fixed; }
        .player-columns > tbody > tr > td { width: 50%; padding: 0 4px; vertical-align: top; border: none; }
        .player-table { table-layout: fixed; }
        .player-table th, .player-table td { font-size: 7.5px; padding: 4px 3px; }
        .player-table .player-name { text-align: left; }
        .subheading td { background: #f1f4f8; color: #43536a; font-weight: bold; text-align: center; }
        .center { text-align: center; }
        .muted { color: #788596; }
        .empty-row { text-align: center; color: #788596; font-style: italic; padding: 8px; }
        .event-table { margin-bottom: 5px; }
        .event-table td { padding: 5px 6px; }
        .notes-box { min-height: 45px; padding: 9px; border: 1px solid #d7dde5; white-space: pre-line; }
        .signature-table { margin-top: 22px; page-break-inside: avoid; }
        .signature-table td { width: 33.33%; text-align: center; padding: 4px 8px; }
        .signature-role { font-weight: bold; color: #35465d; }
        .signature-space { height: 55px; }
        .signature-line { width: 78%; margin: 0 auto; border-top: 1px solid #566273; }
        .footer { margin-top: 15px; padding-top: 7px; border-top: 1px solid #d7dde5; text-align: center; font-size: 7.5px; color: #788596; }
        .page-break-avoid { page-break-inside: avoid; }
    </style>
</head>
<body>

@php
    $homeClub = $match->homeClub;
    $awayClub = $match->awayClub;
    $goals = collect($match->goals ?? []);
    $cards = collect($match->cards ?? []);
    $substitutions = collect($match->substitutions ?? []);
    $matchDate = $match->match_date
        ? \Carbon\Carbon::parse($match->match_date)
        : null;

    $teams = [
        [
            'club' => $homeClub,
            'starters' => collect($homeStarters ?? []),
            'substitutes' => collect($homeSubstitutes ?? []),
        ],
        [
            'club' => $awayClub,
            'starters' => collect($awayStarters ?? []),
            'substitutes' => collect($awaySubstitutes ?? []),
        ],
    ];
@endphp

<table class="header-table">
    <tr>
        <td style="width: 15%; text-align: center;">
            @if(file_exists(public_path('logo1.png')))
                <img src="{{ public_path('logo1.png') }}" class="logo" alt="Logo">
            @elseif(file_exists(public_path('logo.png')))
                <img src="{{ public_path('logo.png') }}" class="logo" alt="Logo">
            @endif
        </td>
        <td style="width: 70%;" class="title">
            <h1>MATCH SUMMARY</h1>
            <h2>{{ strtoupper($match->event->name ?? 'FOOTBALL EVENT') }}</h2>
        </td>
        <td style="width: 15%;" class="report-label">SAPHYR<br>OFFICIAL REPORT</td>
    </tr>
</table>

<div class="section-title">INFORMASI PERTANDINGAN</div>
<table class="match-info-table">
    <thead>
        <tr>
            <th style="width: 12%;">ID Pertandingan</th>
            <th style="width: 28%;">Tanggal &amp; Kick Off</th>
            <th>Stadion</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $match->id ?? '-' }}</td>
            <td>{{ $matchDate ? $matchDate->format('d/m/Y H:i') . ' WIB' : '-' }}</td>
            <td>{{ $match->stadium->name ?? '-' }}</td>
        </tr>
    </tbody>
</table>

<table class="team-score-table page-break-avoid">
    <tr>
        <td style="width: 42%;">
            <div class="team-name">{{ strtoupper($homeClub->name ?? 'TIM KANDANG') }}</div>
        </td>
        <td style="width: 16%;">
            <div class="score">
                {{ $match->result->home_score ?? 0 }} - {{ $match->result->away_score ?? 0 }}
            </div>
            <div class="score-caption">SKOR AKHIR</div>
        </td>
        <td style="width: 42%;">
            <div class="team-name">{{ strtoupper($awayClub->name ?? 'TIM TANDANG') }}</div>
        </td>
    </tr>
</table>

<div class="section-title">DAFTAR PEMAIN</div>
<table class="player-columns">
    <tbody>
        <tr>
            @foreach($teams as $team)
                <td>
                    <div class="team-name" style="font-size: 9px; margin-bottom: 5px;">
                        {{ strtoupper($team['club']->name ?? 'KLUB') }}
                    </div>
                    <table class="player-table">
                        <thead>
                            <tr>
                                <th style="width: 9%;">No</th>
                                <th style="width: 12%;">Pos</th>
                                <th>Nama Pemain</th>
                                <th style="width: 9%;">G</th>
                                <th style="width: 9%;">Y</th>
                                <th style="width: 9%;">R</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($team['starters'] as $slot)
                                @php
                                    $player = $slot->player ?? null;
                                    $playerGoals = $player ? $goals->where('player_id', $player->id)->count() : 0;
                                    $yellowCards = $player ? $cards->where('player_id', $player->id)->where('card_type', 'yellow')->count() : 0;
                                    $redCards = $player ? $cards->where('player_id', $player->id)->where('card_type', 'red')->count() : 0;
                                @endphp
                                <tr>
                                    <td class="center">{{ $player->jersey_number ?? '-' }}</td>
                                    <td class="center">{{ strtoupper($slot->field_position ?? $slot->position ?? '-') }}</td>
                                    <td class="player-name">{{ strtoupper($player->name ?? 'Data pemain tidak tersedia') }}</td>
                                    <td class="center">{{ $playerGoals ?: '-' }}</td>
                                    <td class="center">{{ $yellowCards ?: '-' }}</td>
                                    <td class="center">{{ $redCards ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="empty-row">Belum ada data pemain utama.</td></tr>
                            @endforelse

                            @if($team['substitutes']->isNotEmpty())
                                <tr class="subheading"><td colspan="6">PEMAIN CADANGAN</td></tr>
                            @endif

                            @forelse($team['substitutes'] as $slot)
                                @php
                                    $player = $slot->player ?? null;
                                    $playerGoals = $player ? $goals->where('player_id', $player->id)->count() : 0;
                                    $yellowCards = $player ? $cards->where('player_id', $player->id)->where('card_type', 'yellow')->count() : 0;
                                    $redCards = $player ? $cards->where('player_id', $player->id)->where('card_type', 'red')->count() : 0;
                                @endphp
                                <tr>
                                    <td class="center">{{ $player->jersey_number ?? '-' }}</td>
                                    <td class="center">{{ strtoupper($slot->field_position ?? $slot->position ?? '-') }}</td>
                                    <td class="player-name">{{ strtoupper($player->name ?? 'Data pemain tidak tersedia') }}</td>
                                    <td class="center">{{ $playerGoals ?: '-' }}</td>
                                    <td class="center">{{ $yellowCards ?: '-' }}</td>
                                    <td class="center">{{ $redCards ?: '-' }}</td>
                                </tr>
                            @empty
                                @if($team['starters']->isEmpty())
                                    <tr><td colspan="6" class="empty-row">Belum ada data pemain cadangan.</td></tr>
                                @endif
                            @endforelse
                        </tbody>
                    </table>
                </td>
            @endforeach
        </tr>
    </tbody>
</table>

<div class="section-title">PENCETAK GOL</div>
<table class="event-table">
    <thead>
        <tr>
            <th style="width: 15%;">Menit</th>
            <th>Nama Pemain</th>
            <th style="width: 30%;">Klub</th>
            <th style="width: 15%;">Tipe Gol</th>
        </tr>
    </thead>
    <tbody>
        @forelse($goals as $goal)
            @php
                $goalClubName = $goal->club_id == $match->home_club_id
                    ? ($homeClub->name ?? 'Tim Kandang')
                    : ($awayClub->name ?? 'Tim Tandang');
            @endphp
            <tr>
                <td class="center">{{ $goal->minute_label ?? $goal->minute ?? '-' }}{{ ($goal->minute_label ?? $goal->minute) !== null ? "'" : '' }}</td>
                <td>{{ strtoupper($goal->player->name ?? '-') }}</td>
                <td>{{ strtoupper($goalClubName) }}</td>
                <td class="center">{{ $goal->is_penalty ? 'PENALTI' : 'NORMAL' }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="empty-row">Belum ada gol yang tercatat.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="section-title">PERGANTIAN PEMAIN</div>
<table class="event-table">
    <thead>
        <tr>
            <th style="width: 15%;">Menit</th>
            <th>Pemain Keluar</th>
            <th>Pemain Masuk</th>
            <th style="width: 25%;">Klub</th>
        </tr>
    </thead>
    <tbody>
        @forelse($substitutions as $sub)
            @php
                $subClubName = $sub->club_id == $match->home_club_id
                    ? ($homeClub->name ?? 'Tim Kandang')
                    : ($awayClub->name ?? 'Tim Tandang');
                $subMinute = $sub->minute_label ?? $sub->minute;
            @endphp
            <tr>
                <td class="center">{{ $subMinute ?? '-' }}{{ $subMinute !== null ? "'" : '' }}</td>
                <td>{{ strtoupper($sub->playerOut->name ?? '-') }}</td>
                <td>{{ strtoupper($sub->playerIn->name ?? '-') }}</td>
                <td>{{ strtoupper($subClubName) }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="empty-row">Tidak ada pergantian pemain.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="section-title">CATATAN PERTANDINGAN</div>
<div class="notes-box">{{ filled($match->summary ?? null) ? $match->summary : 'Belum ada catatan pertandingan.' }}</div>

<div class="section-title">PENGESAHAN</div>
<table class="signature-table">
    <tr>
        <td>
            <div class="signature-role">Pelatih {{ strtoupper($homeClub->name ?? 'TIM KANDANG') }}</div>
            <div class="signature-space"></div>
            <div class="signature-line"></div>
        </td>
        <td>
            <div class="signature-role">Wasit Utama</div>
            <div class="signature-space"></div>
            <div class="signature-line"></div>
        </td>
        <td>
            <div class="signature-role">Pelatih {{ strtoupper($awayClub->name ?? 'TIM TANDANG') }}</div>
            <div class="signature-space"></div>
            <div class="signature-line"></div>
        </td>
    </tr>
</table>

<div class="footer">
    <strong>SAPHYR — Match Summary Report</strong><br>
    Dokumen ini dibuat secara otomatis oleh sistem SAPHYR.<br>
    Dicetak pada: {{ now()->format('d F Y, H:i:s') }} WIB
</div>

</body>
</html>

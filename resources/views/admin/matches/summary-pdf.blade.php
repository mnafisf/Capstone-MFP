<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Match Summary Report</title>
    @include('partials.pdf.dsp-styles')
    <style>
        .logo {
            width: 55px;
        }

        .header-table {
            border: none;
        }

        .header-table td {
            border: none;
        }

        .title {
            text-align: center;
        }

        .title h1 {
            font-size: 22px;
            margin-bottom: 2px;
        }

        .title h2 {
            font-size: 14px;
            font-weight: bold;
        }

        .team-name {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }

        .score {
            font-size: 28px;
            font-weight: bold;
            text-align: center;
        }

        .section-title {
            margin-top: 8px;
            margin-bottom: 4px;
            background: #efefef;
            border: 1px solid #000;
            padding: 4px;
            font-weight: bold;
        }

        .player-table th,
        .player-table td {
            font-size: 8px;
            padding: 2px;
        }

        .signature {
            height: 45px;
        }

        .footer {
            margin-top: 12px;
            font-size: 8px;
            text-align: center;
        }

        .gray {
            background: #f5f5f5;
        }

        .match-info-table {
            margin-top: 8px;
        }

        .team-score-table {
            margin-top: 5px;
        }

        .goals-table,
        .substitutions-table,
        .notes-table {
            width: 100%;
        }

        .signature-table {
            margin-top: 15px;
            border: none;
        }

        .signature-table td {
            border: none;
        }

        hr {
            margin-top: 20px;
        }
    </style>
</head>

<body>

<!-- HEADER -->

<table class="header-table">

<tr>

<td style="width:15%; text-align:center;">

@if(file_exists(public_path('logo1.png')))

<img src="{{ public_path('logo1.png') }}" class="logo">

@elseif(file_exists(public_path('logo.png')))

<img src="{{ public_path('logo.png') }}" class="logo">

@endif

</td>

<td style="width:70%;" class="title">

<h1>MATCH SUMMARY</h1>

<h2>{{ strtoupper($match->event->name ?? 'FOOTBALL EVENT') }}</h2>

</td>

<td style="width:15%;">

</td>

</tr>

</table>

<!-- MATCH INFORMATION -->

<table class="match-info-table">

    <tr>

        <th style="width:8%">No</th>

        <th style="width:22%">Tanggal & Kick Off</th>

        <th>Stadion</th>

    </tr>

    <tr>

        <td class="text-center">
            {{ $match->id }}
        </td>

        <td class="text-center">
            {{ $match->match_date->format('d/m/Y H:i') }} WIB
        </td>

        <td class="text-center">
            {{ $match->stadium->name ?? '-' }}
        </td>

    </tr>

</table>

<table class="team-score-table">

<tr>

<td style="width:42%;">

<div class="team-name">

{{ strtoupper($match->homeClub->name) }}

</div>

</td>

<td style="width:16%;" class="text-center">

<div class="score">

{{ $match->result?->home_score ?? 0 }}

-

{{ $match->result?->away_score ?? 0 }}

</div>

</td>

<td style="width:42%;">

<div class="team-name">

{{ strtoupper($match->awayClub->name) }}

</div>

</td>

</tr>

</table>

<div class="section-title">
    DAFTAR PEMAIN
</div>

<table class="player-table">

<tr>

<!-- HOME -->

<td style="width:50%; vertical-align:top;">

<table>

<thead>

<tr>

<th style="width:8%">No</th>

<th style="width:10%">Pos</th>

<th>Nama Pemain</th>

<th style="width:6%">G</th>

<th style="width:6%">Y</th>

<th style="width:6%">R</th>

</tr>

</thead>

<tbody>

@foreach($homeStarters as $slot)

@php

$player = $slot->player;

$goal = $match->goals
            ->where('player_id',$player->id)
            ->count();

$yellow = $match->cards
            ->where('player_id',$player->id)
            ->where('card_type','yellow')
            ->count();

$red = $match->cards
            ->where('player_id',$player->id)
            ->where('card_type','red')
            ->count();

@endphp

<tr>

<td class="text-center">
{{ $player->jersey_number ?? '-' }}
</td>

<td class="text-center">
{{ strtoupper($slot->field_position ?? '-') }}
</td>

<td>
{{ strtoupper($player->name) }}
</td>

<td class="text-center">
{{ $goal > 0 ? $goal : '-' }}
</td>

<td class="text-center">
{{ $yellow > 0 ? $yellow : '-' }}
</td>

<td class="text-center">
{{ $red > 0 ? $red : '-' }}
</td>

</tr>

@endforeach

@if($homeSubstitutes->count())

<tr>

<td colspan="6" class="gray bold text-center">

PEMAIN CADANGAN

</td>

</tr>

@endif

@foreach($homeSubstitutes as $slot)

@php

$player = $slot->player;

$goal = $match->goals
            ->where('player_id',$player->id)
            ->count();

$yellow = $match->cards
            ->where('player_id',$player->id)
            ->where('card_type','yellow')
            ->count();

$red = $match->cards
            ->where('player_id',$player->id)
            ->where('card_type','red')
            ->count();

@endphp

<tr>

<td class="text-center">
{{ $player->jersey_number ?? '-' }}
</td>

<td class="text-center">
{{ strtoupper($slot->field_position ?? '-') }}
</td>

<td>
{{ strtoupper($player->name) }}
</td>

<td class="text-center">
{{ $goal > 0 ? $goal : '-' }}
</td>

<td class="text-center">
{{ $yellow > 0 ? $yellow : '-' }}
</td>

<td class="text-center">
{{ $red > 0 ? $red : '-' }}
</td>

</tr>

@endforeach

</tbody>

</table>

</td>

<!-- AWAY -->

<td style="width:50%; vertical-align:top;">

<table>

<thead>

<tr>

<th style="width:8%">No</th>

<th style="width:10%">Pos</th>

<th>Nama Pemain</th>

<th style="width:6%">G</th>

<th style="width:6%">Y</th>

<th style="width:6%">R</th>

</tr>

</thead>

<tbody>

@foreach($awayStarters as $slot)

@php

$player = $slot->player;

$goal = $match->goals
            ->where('player_id',$player->id)
            ->count();

$yellow = $match->cards
            ->where('player_id',$player->id)
            ->where('card_type','yellow')
            ->count();

$red = $match->cards
            ->where('player_id',$player->id)
            ->where('card_type','red')
            ->count();

@endphp

<tr>

<td class="text-center">
{{ $player->jersey_number ?? '-' }}
</td>

<td class="text-center">
{{ strtoupper($slot->field_position ?? '-') }}
</td>

<td>
{{ strtoupper($player->name) }}
</td>

<td class="text-center">
{{ $goal > 0 ? $goal : '-' }}
</td>

<td class="text-center">
{{ $yellow > 0 ? $yellow : '-' }}
</td>

<td class="text-center">
{{ $red > 0 ? $red : '-' }}
</td>

</tr>

@endforeach

@if($awaySubstitutes->count())

<tr>

<td colspan="6" class="gray bold text-center">

PEMAIN CADANGAN

</td>

</tr>

@endif

@foreach($awaySubstitutes as $slot)

@php

$player = $slot->player;

$goal = $match->goals
            ->where('player_id',$player->id)
            ->count();

$yellow = $match->cards
            ->where('player_id',$player->id)
            ->where('card_type','yellow')
            ->count();

$red = $match->cards
            ->where('player_id',$player->id)
            ->where('card_type','red')
            ->count();

@endphp

<tr>

<td class="text-center">
{{ $player->jersey_number ?? '-' }}
</td>

<td class="text-center">
{{ strtoupper(substr($slot->position ?? '-',0,2)) }}
</td>

<td>
{{ strtoupper($player->name) }}
</td>

<td class="text-center">
{{ $goal > 0 ? $goal : '-' }}
</td>

<td class="text-center">
{{ $yellow > 0 ? $yellow : '-' }}
</td>

<td class="text-center">
{{ $red > 0 ? $red : '-' }}
</td>

</tr>

@endforeach

</tbody>

</table>

</td>

</tr>

</table>

<div class="section-title">
    PENCETAK GOL
</div>

<table class="goals-table">

<thead>

<tr>

<th width="15%">Menit</th>
<th>Nama Pemain</th>
<th width="30%">Klub</th>
<th width="15%">Tipe</th>

</tr>

</thead>

<tbody>

@forelse($match->goals as $goal)

<tr>

<td class="text-center">
{{ $goal->minute_label }}'
</td>

<td>
{{ strtoupper($goal->player?->name ?? '-') }}
</td>

<td>
{{ $goal->club_id == $match->home_club_id
? strtoupper($match->homeClub->name)
: strtoupper($match->awayClub->name) }}
</td>

<td class="text-center">
{{ $goal->is_penalty ? 'PEN' : 'GOAL' }}
</td>

</tr>

@empty

<tr>

<td colspan="4" class="text-center">
Belum ada gol.
</td>

</tr>

@endforelse

</tbody>

</table>

<div class="section-title">
    PERGANTIAN PEMAIN
</div>

<table class="substitutions-table">

<thead>

<tr>

<th width="15%">Menit</th>

<th>Keluar</th>

<th>Masuk</th>

<th width="25%">Klub</th>

</tr>

</thead>

<tbody>

@forelse($match->substitutions as $sub)

<tr>

<td class="text-center">

{{ $sub->minute_label }}'

</td>

<td>

{{ strtoupper($sub->playerOut->name ?? '-') }}

</td>

<td>

{{ strtoupper($sub->playerIn->name ?? '-') }}

</td>

<td>

{{ $sub->club_id == $match->home_club_id
? strtoupper($match->homeClub->name)
: strtoupper($match->awayClub->name) }}

</td>

</tr>

@empty

<tr>

<td colspan="4" class="text-center">

Tidak ada pergantian pemain.

</td>

</tr>

@endforelse

</tbody>

</table>

<div class="section-title">

CATATAN PERTANDINGAN

</div>

<table class="notes-table">

<tr>

<td style="padding:10px;">

@if($match->summary)

{{ $match->summary }}

@else

Belum ada catatan pertandingan.

@endif

</td>

</tr>

</table>

<div class="section-title">

PENGESAHAN

</div>

<table class="signature-table">

<tr>

<td style="width:33%; text-align:center;">

Pelatih {{ strtoupper($match->homeClub->name) }}

<br><br><br><br>

_________________________

</td>

<td style="width:33%; text-align:center;">

Wasit Utama

<br><br><br><br>

_________________________

</td>

<td style="width:33%; text-align:center;">

Pelatih {{ strtoupper($match->awayClub->name) }}

<br><br><br><br>

_________________________

</td>

</tr>

</table>

<hr>

<div class="footer">

SAPHYR Match Report

<br>

Dicetak pada: {{ now()->format('d F Y H:i:s') }}

</div>

</body>

</html>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>DSP Pertandingan</title>
    @include('partials.pdf.dsp-styles')
    <style>
        table.roster th,
        table.roster td {
            font-size: 10px;
            padding: 4px 5px;
        }

        .officials-table th,
        .officials-table td {
            border: 1px solid #d1d5db;
            padding: 4px 5px;
            font-size: 10px;
        }

        .officials-table {
            width: 100%;
            border-collapse: collapse;
        }

        .officials-table th {
            background: #f3f4f6;
            font-weight: 700;
        }

        .notes {
            white-space: pre-wrap;
            line-height: 1.5;
            font-size: 10.5px;
        }

        .dsp-side-col {
            width: 300px;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="dsp-header">
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td valign="top">
                        <div class="dsp-title">DAFTAR SUSUNAN PEMAIN (DSP)</div>
                        <div class="dsp-subtitle">FORM C2 · {{ $match->event->name ?? '-' }}</div>
                    </td>
                    <td valign="top" align="right" style="font-size:11px; line-height:1.5;">
                        {{ $match->match_date->format('d M Y H:i') }}
                    </td>
                </tr>
            </table>
        </div>

        <table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:10px;">
            <tr>
                <td width="50%" valign="top" style="padding-right:5px;">
                    <div class="dsp-meta">
                        <strong>No. Pertandingan:</strong> {{ $match->id }}<br>
                        <strong>Venue:</strong> {{ $match->stadium?->name ?? '-' }}<br>
                        <strong>Kick Off:</strong> {{ $match->match_date->format('H:i') }}
                    </div>
                </td>
                <td width="50%" valign="top" style="padding-left:5px;">
                    <div class="dsp-meta">
                        <strong>Home:</strong> {{ $match->homeClub->name }}<br>
                        <strong>Away:</strong> {{ $match->awayClub->name }}
                    </div>
                </td>
            </tr>
        </table>

        <table class="dsp-layout" width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td class="dsp-main-col" valign="top">
                    <div class="dsp-card">
                        <div class="dsp-card-title">{{ $match->homeClub->name }} - Starting XI</div>
                        <table class="roster">
                            <thead>
                                <tr>
                                    <th style="width:55px">No Punggung</th>
                                    <th style="width:80px">Pos</th>
                                    <th>Nama Pemain</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($homeStarters as $p)
                                    <tr>
                                        <td>{{ $p->player?->jersey_number ?? '-' }}</td>
                                        <td>{{ $p->field_position ?: '-' }}</td>
                                        <td>{{ $p->player?->name ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3">—</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="dsp-card">
                        <div class="dsp-card-title">{{ $match->homeClub->name }} - Substitutes</div>
                        <table class="roster">
                            <thead>
                                <tr>
                                    <th style="width:55px">No Punggung</th>
                                    <th style="width:80px">Pos</th>
                                    <th>Nama Pemain</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($homeSubstitutes as $p)
                                    <tr>
                                        <td>{{ $p->player?->jersey_number ?? '-' }}</td>
                                        <td>{{ $p->field_position ?: '-' }}</td>
                                        <td>{{ $p->player?->name ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3">—</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="dsp-card">
                        <div class="dsp-card-title">{{ $match->awayClub->name }} - Starting XI</div>
                        <table class="roster">
                            <thead>
                                <tr>
                                    <th style="width:55px">No Punggung</th>
                                    <th style="width:80px">Pos</th>
                                    <th>Nama Pemain</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($awayStarters as $p)
                                    <tr>
                                        <td>{{ $p->player?->jersey_number ?? '-' }}</td>
                                        <td>{{ $p->field_position ?: '-' }}</td>
                                        <td>{{ $p->player?->name ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3">—</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="dsp-card">
                        <div class="dsp-card-title">{{ $match->awayClub->name }} - Substitutes</div>
                        <table class="roster">
                            <thead>
                                <tr>
                                    <th style="width:55px">No Punggung</th>
                                    <th style="width:80px">Pos</th>
                                    <th>Nama Pemain</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($awaySubstitutes as $p)
                                    <tr>
                                        <td>{{ $p->player?->jersey_number ?? '-' }}</td>
                                        <td>{{ $p->field_position ?: '-' }}</td>
                                        <td>{{ $p->player?->name ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3">—</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </td>

                <td class="dsp-side-col" valign="top">
                    @include('partials.pdf.dsp-jersey-colors', [
                        'title' => $match->homeClub->name.' - Warna Jersey',
                        'shirtColor' => $homeLineup?->shirt_color ?? $match->homeClub->shirt_color,
                        'pantsColor' => $homeLineup?->pants_color ?? $match->homeClub->pants_color,
                        'socksColor' => $homeLineup?->socks_color ?? $match->homeClub->socks_color,
                    ])

                    @include('partials.pdf.dsp-jersey-colors', [
                        'title' => $match->awayClub->name.' - Warna Jersey',
                        'shirtColor' => $awayLineup?->shirt_color ?? $match->awayClub->shirt_color,
                        'pantsColor' => $awayLineup?->pants_color ?? $match->awayClub->pants_color,
                        'socksColor' => $awayLineup?->socks_color ?? $match->awayClub->socks_color,
                    ])

                    <div class="dsp-card">
                        <div class="dsp-card-title">Official Tim - Home</div>
                        <table class="officials-table">
                            <thead>
                                <tr>
                                    <th>Jabatan</th>
                                    <th>Nama</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($match->homeClub->officials ?? collect()) as $off)
                                    <tr>
                                        <td>{{ $off->position_label }}</td>
                                        <td>{{ $off->name }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2">—</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="dsp-card">
                        <div class="dsp-card-title">Official Tim - Away</div>
                        <table class="officials-table">
                            <thead>
                                <tr>
                                    <th>Jabatan</th>
                                    <th>Nama</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($match->awayClub->officials ?? collect()) as $off)
                                    <tr>
                                        <td>{{ $off->position_label }}</td>
                                        <td>{{ $off->name }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2">—</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="sig-block">
                        <div class="sig-title">Manager Tim</div>
                        <div class="sig-line">Tanda tangan</div>
                    </div>

                    <div class="sig-block">
                        <div class="sig-title">Pelatih Tim</div>
                        <div class="sig-line">Tanda tangan</div>
                    </div>
                </td>
            </tr>
        </table>

        @if ($match->match_notes)
            <div class="dsp-card">
                <div class="dsp-card-title">Catatan Pertandingan</div>
                <div class="notes">{{ $match->match_notes }}</div>
            </div>
        @endif
    </div>
</body>eds
</html>

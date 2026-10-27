<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>DSP - Daftar Susunan Pemain</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #000;
            margin: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: middle;
        }
        th {
            background: #DBEAFE;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .player-table, .side-box, .official-table {
            page-break-inside: avoid;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
    </style>
</head>
<body>

<!-- ================= HEADER ================= -->

<table width="100%" cellspacing="0" cellpadding="0" style="border-bottom:3px solid #1D4ED7; padding-bottom:10px; margin-bottom:15px;">

    <tr>

        <!-- Logo SAPHYR -->
        <td width="15%" align="left" valign="middle">

            @if(file_exists(public_path('logo1.png')))
                <img src="{{ public_path('logo1.png') }}" style="width:70px;">
            @elseif(file_exists(public_path('logo.png')))
                <img src="{{ public_path('logo.png') }}" style="width:70px;">
            @endif

        </td>

        <!-- Judul -->
        <td width="70%" align="center" valign="middle">

            <div style="
                font-size:22px;
                font-weight:bold;
                color:#1D4ED7;
                letter-spacing:1px;
                margin-bottom:3px;
            ">
                DAFTAR SUSUNAN PEMAIN
            </div>

            <div style="
                font-size:11px;
                color:#6B7280;  
                margin-bottom:4px;
            ">
                Smart Athlete Performance &amp; Health Yield Reporting
            </div>

            <div style="
                font-size:13px;
                font-weight:bold;
                color:#111827;
                text-transform:uppercase;
            ">
                {{ $lineup->match->event->name ?? '-' }}
            </div>

        </td>

        <!-- Logo Club -->
        <td width="15%" align="right" valign="middle">
            @if($lineup->club && $lineup->club->logo_path && file_exists(storage_path('app/public/' . $lineup->club->logo_path)))
                <img src="{{ storage_path('app/public/' . $lineup->club->logo_path) }}" style="width:70px; height:70px;">
            @elseif($lineup->club)
                <div style="
                    width:70px;
                    height:70px;
                    border:2px solid #1D4ED7;
                    border-radius:50%;
                    text-align:center;
                    line-height:70px;
                    font-size:8px;
                    color:#1D4ED7;
                    font-weight:bold;
                    background:#f0f9ff;
                ">
                    {{ strtoupper(substr($lineup->club->name, 0, 2)) }}
                </div>
            @endif
        </td>

    </tr>

</table>

<!-- ================= MATCH INFORMATION ================= -->

<table width="100%" cellspacing="0" cellpadding="5" style="margin-bottom:15px; border-collapse:collapse;">

    <thead>

        <tr style="background:#1D4ED7; color:white;">

            <th width="10%">No</th>

            <th width="30%">Tanggal</th>

            <th>Stadion</th>

            <th width="20%">Kick Off</th>

        </tr>

    </thead>

    <tbody>

        <tr>

            <td align="center">
                {{ $lineup->match->id }}
            </td>

            <td align="center">
                {{ $lineup->match->match_date->format('d M Y') }}
            </td>

            <td align="center">
                {{ $lineup->match->stadium?->name ?? '-' }}
            </td>

            <td align="center">
                {{ $lineup->match->match_date->format('H:i') }}
            </td>

        </tr>

    </tbody>

</table>

<!-- ================= CLUB NAME ================= -->

<table width="100%" cellspacing="0" cellpadding="6" style="margin-bottom:15px; border-collapse:collapse;">

    <tr>

        <td width="18%" style="
            background:#1D4ED7;
            color:white;
            font-weight:bold;
        ">
            NAMA KLUB
        </td>

        <td style="
            font-weight:bold;
            font-size:12px;
            text-transform:uppercase;
        ">
            {{ $lineup->club->name ?? '-' }}
        </td>

    </tr>

</table>

<table width="100%" cellspacing="8" cellpadding="0">

<tr>

<!-- ========================= LEFT ========================= -->

<td width="75%" valign="top">

<div style="
background:#1D4ED7;
color:white;
padding:6px;
font-size:12px;
font-weight:bold;
margin-bottom:5px;
">

DAFTAR PEMAIN

</div>

<table width="100%" cellspacing="0" cellpadding="5" style="border-collapse:collapse;">

<thead>

<tr style="background:#DBEAFE;">

<th width="10%">No</th>

<th width="12%">Pos</th>

<th>Nama Pemain</th>

</tr>

</thead>

<tbody>

@forelse($starters as $index => $p)

<tr>

<td align="center">

{{ $p->player?->jersey_number ?? '-' }}

</td>

<td align="center">

{{ $p->field_position ?? '-' }}

</td>

<td>

<strong>

{{ strtoupper($p->player?->name ?? '-') }}

</strong>

</td>

</tr>

@empty

<tr>

<td colspan="3" align="center">

Belum ada pemain utama

</td>

</tr>

@endforelse

<tr>

<td colspan="3"
style="
background:#1D4ED7;
color:white;
font-weight:bold;
text-align:center;
">

PEMAIN CADANGAN

</td>

</tr>

@forelse($substitutes as $p)

<tr>

<td align="center">

{{ $p->player?->jersey_number ?? '-' }}

</td>

<td align="center">

{{ $p->field_position ?? '-' }}

</td>

<td>

{{ strtoupper($p->player?->name ?? '-') }}

</td>

</tr>

@empty

<tr>

<td colspan="3" align="center">

Tidak ada pemain cadangan

</td>

</tr>

@endforelse

</tbody>

</table>

</td>

<td width="25%" valign="top" style="padding-left:10px;">

<div style="
background:#1D4ED7;
color:white;
padding:8px;
font-weight:bold;
text-align:center;
margin-bottom:10px;
font-size:12px;
">

WARNA JERSEY

</div>

<table width="100%" cellpadding="8" style="border-collapse:collapse;">

<tr style="background:#DBEAFE;">

<td style="border:1px solid #000; font-weight:bold;">Baju</td>

</tr>

<tr>

<td style="border:1px solid #000; text-align:center; padding:15px;">

{{ $lineup->shirt_color ?? '-' }}

</td>

</tr>

<tr style="background:#DBEAFE;">

<td style="border:1px solid #000; font-weight:bold;">Celana</td>

</tr>

<tr>

<td style="border:1px solid #000; text-align:center; padding:15px;">

{{ $lineup->pants_color ?? '-' }}

</td>

</tr>

<tr style="background:#DBEAFE;">

<td style="border:1px solid #000; font-weight:bold;">Kaos Kaki</td>

</tr>

<tr>

<td style="border:1px solid #000; text-align:center; padding:15px;">

{{ $lineup->socks_color ?? '-' }}

</td>

</tr>

</table>

</td>

</tr>

</table>

<div style="margin-top:20px; page-break-before: always;">

<div style="
background:#1D4ED7;
color:white;
padding:8px;
text-align:center;
font-weight:bold;
font-size:12px;
margin-bottom:10px;
">

OFFICIAL TIM

</div>

<table class="official-table" width="100%" cellspacing="0" cellpadding="5" style="border-collapse:collapse;">

<thead>

<tr style="background:#DBEAFE;">

<th width="10%">No</th>

<th>Nama Official</th>

<th width="30%">Jabatan</th>

</tr>

</thead>

<tbody>

@forelse($lineup->club->officials ?? [] as $index => $official)

<tr>

<td class="text-center">{{ $index + 1 }}</td>

<td>{{ strtoupper($official->name ?? '-') }}</td>

<td>{{ strtoupper($official->position ?? '-') }}</td>

</tr>

@empty

<tr>

<td colspan="3" align="center">Belum ada data official</td>

</tr>

@endforelse

</tbody>

</table>


<div style="margin-top:25px; page-break-inside:avoid;">

<div style="
background:#1D4ED7;
color:white;
padding:8px;
text-align:center;
font-weight:bold;
font-size:12px;
margin-bottom:15px;
">

PENGESAHAN

</div>

<table width="100%" cellpadding="0" cellspacing="0" style="border:none;">

<tr style="border:none;">

<td width="33%" align="center" style="border:none; padding:20px 5px;">

<div style="border-bottom:1px solid #000; height:60px; line-height:60px;"></div>

Manager Tim

</td>

<td width="33%" align="center" style="border:none; padding:20px 5px;">

<div style="border-bottom:1px solid #000; height:60px; line-height:60px;"></div>

Pelatih Tim

</td>

<td width="33%" align="center" style="border:none; padding:20px 5px;">

<div style="border-bottom:1px solid #000; height:60px; line-height:60px;"></div>

Wasit Pertandingan

</td>

</tr>

</table>

</div>

<hr style="margin-top:20px; border:0; border-top:2px solid #1D4ED7;">

<div style="text-align:center; margin-top:15px; font-size:9px; color:#666;">

<strong>SAPHYR</strong> - Smart Athlete Performance &amp; Health Yield Reporting

<br>

Generated: {{ now()->format('d F Y H:i:s') }}

</div>

</body>

</html>
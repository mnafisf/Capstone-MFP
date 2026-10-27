<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'license_type',
        'license_number',
        'phone',
        'ktp_path',
        'license_certificate_path',
    ];
}

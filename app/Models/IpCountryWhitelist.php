<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpCountryWhitelist extends Model
{
    protected $table = 'ip_country_whitelist';

    protected $fillable = [
        'country_code',
        'country_name',
    ];
}

<?php

// namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\Models\Setting;
use Carbon\Carbon;

// class TimeHelper
// {
//     /**
//      * Konversi singkatan zona (WIB/WITA/WIT) ke timezone PHP
//      */
//     public static function zoneToTimezone(string $zone): string
//     {
//         return match(strtoupper($zone)) {
//             'WIB'  => 'Asia/Jakarta',
//             'WITA' => 'Asia/Makassar',
//             'WIT'  => 'Asia/Jayapura',
//             default => 'UTC',
//         };
//     }

//     /**
//      * Ambil waktu sekarang sesuai zona
//      */
//     public static function now(string $zone = 'WIB'): Carbon
//     {
//         return Carbon::now(self::zoneToTimezone($zone));
//     }

//     /**
//      * Convert timestamp ke zona tertentu
//      */
//     public static function toZone($datetime, string $zone = 'WIB'): Carbon
//     {
//         return Carbon::parse($datetime)->timezone(self::zoneToTimezone($zone));
//     }
// }

if (!function_exists('setting')) {
    function setting($key, $default = null) {
        return cache()->rememberForever("setting_{$key}", function () use ($key, $default) {
            return Setting::where('key', $key)->value('value') ?? $default;
        });
    }
} 


<?php
 
use Illuminate\Support\Facades\DB;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;  

if (!function_exists('setting')) {
    function setting($key, $default = null) {
        try {
            // cek koneksi DB
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            // DB belum ada, return default
            return $default;
        }

        // cek tabel ada
        if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            return $default;
        }

        return cache()->rememberForever("setting_{$key}", function () use ($key, $default) {
            return Setting::where('key', $key)->value('value') ?? $default;
        });
    }
}

if (!function_exists('safeAddMinutes')) {
    function safeAddMinutes(Carbon $date, $minutes): Carbon {
        $minutes = is_numeric($minutes) ? $minutes + 0 : 0;
        return $date->addMinutes($minutes);
    }
}

if (!function_exists('safeAddHours')) {
    function safeAddHours(Carbon $date, $hours): Carbon {
        $hours = is_numeric($hours) ? $hours + 0 : 0;
        return $date->addHours($hours);
    }
}

if (!function_exists('safeCarbonUnit')) {
    function safeCarbonUnit(Carbon $date, string $unit, $value): Carbon {
        $value = is_numeric($value) ? $value + 0 : null;
        return $date->setUnit($unit, $value);
    }
}
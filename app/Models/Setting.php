<?php
// app/Models/Setting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description'
    ];

    protected static function booted()
    {
        static::saved(function ($setting) {
            // Hapus cache untuk key ini setelah update atau create
            Cache::forget("setting_{$setting->key}");
        });

        static::deleted(function ($setting) {
            // Hapus cache juga jika dihapus
            Cache::forget("setting_{$setting->key}");
        });
    }

    public function getValueAttribute($value)
    {
        switch ($this->type) {
            case 'boolean':
                return (bool) $value;
            case 'integer':
                return (int) $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    public function setValueAttribute($value)
    {
        switch ($this->type) {
            case 'boolean':
                $this->attributes['value'] = $value ? '1' : '0';
                break;
            case 'json':
                $this->attributes['value'] = json_encode($value);
                break;
            default:
                $this->attributes['value'] = $value;
        }
    }

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
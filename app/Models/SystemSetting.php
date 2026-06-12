<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $primaryKey = 'key';
    protected $keyType    = 'string';
    public    $incrementing = false;

    protected $fillable = ['key', 'value', 'label', 'group'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $row = static::find($key);
        return $row ? $row->value : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::where('key', $key)->update(['value' => $value]);
    }

    public static function isOtpEnabled(): bool
    {
        return static::get('otp_login_enabled', '1') === '1';
    }
}

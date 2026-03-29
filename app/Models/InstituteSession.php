<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstituteSession extends Model
{
    protected $table = 'institute_sessions';

    protected $fillable = [
        'institute_id', 'name', 'start_date', 'end_date', 'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'boolean',
    ];

    public function institute()
    {
        return $this->belongsTo(\App\Models\Owner\Institute::class);
    }

    // Activate this session, deactivate all others of same institute
    public function activate(): void
    {
        // Pehle sab band karo
        static::where('institute_id', $this->institute_id)
            ->where('id', '!=', $this->id)
            ->update(['is_active' => false]);

        // Ab yeh activate karo
        $this->update(['is_active' => true]);
    }
}
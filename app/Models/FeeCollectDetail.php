<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class FeeCollectDetail extends Model
{
    protected $table = 'fee_collect_details';

    protected $fillable = [
        'institute_id', 'user_id', 'invoice_no',
        'payment_mode', 'utr', 'amount', 'amt', 'date',
        'note', 'received_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function amountColumn(): string
    {
        return Schema::hasColumn('fee_collect_details', 'amount') ? 'amount' : 'amt';
    }

    public function getAmountValueAttribute()
    {
        $column = static::amountColumn();

        return $this->{$column};
    }
}

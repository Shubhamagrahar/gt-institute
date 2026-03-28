<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchDetail extends Model
{
    protected $table = 'batch_details';
    protected $fillable = ['institute_id', 'name', 'start_time', 'end_time', 'status'];
}

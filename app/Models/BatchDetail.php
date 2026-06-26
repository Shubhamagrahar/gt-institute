<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchDetail extends Model
{
    protected $table = 'batch_details';
    protected $fillable = ['institute_id', 'franchise_id', 'name', 'start_time', 'end_time', 'status'];

    public function scopeForInstitute($query, int $iid)
    {
        return $query->where('institute_id', $iid)->whereNull('franchise_id');
    }

    public function scopeForFranchise($query, int $fid)
    {
        return $query->where('franchise_id', $fid);
    }
}

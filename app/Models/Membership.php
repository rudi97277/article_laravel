<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $guarded = [];
    public $incrementing = false;

    public function evidence()
    {
        return $this->belongsTo(Document::class, 'evidence_id');
    }
}

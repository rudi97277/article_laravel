<?php

namespace App\Models;

use App\Traits\UuidGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory, UuidGenerator;
    protected $guarded = ['id'];

    public function getUrlAttribute($value)
    {
        if (empty($value)) return null;
        return url($value);
    }
}

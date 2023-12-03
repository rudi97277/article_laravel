<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    protected $guarded = ['id'];

    public function modelDocuments()
    {
        return $this->morphMany(ModelDocument::class, 'documentable');
    }

    public function documents()
    {
        return $this->hasManyDeepFromRelations($this->modelDocuments(), (new ModelDocument())->document());
    }

    public function cover()
    {
        return $this->belongsTo(Document::class, 'cover_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

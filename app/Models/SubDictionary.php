<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImage;

class SubDictionary extends Model
{
    use HasImage;
    protected $fillable = ['dictionary_id', 'title', 'image'];

    public function dictionary()
    {
        return $this->belongsTo(Dictionary::class, 'dictionary_id', 'id');
    }

    public function childEntries()
    {
        return $this->hasMany(ChieldDictionary::class, 'sub_dictionary_id', 'id');
    }
}

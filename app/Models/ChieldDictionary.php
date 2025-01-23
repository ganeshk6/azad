<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImage;

class ChieldDictionary extends Model
{
    use HasImage;

    protected $table = 'chield_dictionaries';
    protected $fillable = ['dictionary_id', 'title', 'image', 'sub_dictionary_id'];

    public function subDictionary()
    {
        return $this->belongsTo(SubDictionary::class, 'sub_dictionary_id', 'id');
    }

    public function dictionary()
    {
        return $this->belongsTo(Dictionary::class, 'dictionary_id', 'id');
    }


}

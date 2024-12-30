<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phrase extends Model
{
    public function PhraseWord()
    {
        return $this->hasMany(PhraseWord::class);
    }
}

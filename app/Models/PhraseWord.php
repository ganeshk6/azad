<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhraseWord extends Model
{
    protected $table = 'phrase_words'; 

    protected $fillable = ['phrase_id', 'word', 'description', 'signature'];

}

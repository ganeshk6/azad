<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubDictionary extends Model
{
    protected $fillable = ['dictionary_id', 'sub_word', 'sub_description'];

}

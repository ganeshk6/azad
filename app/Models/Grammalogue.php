<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grammalogue extends Model
{
    public function subEntries()
    {
        return $this->hasMany(SubGrammalogue::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outline extends Model
{
    public function OutlineSearch()
    {
        return $this->hasMany(SearchOutline::class);
    }
}

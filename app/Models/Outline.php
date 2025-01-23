<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImage;

class Outline extends Model
{
    use HasImage;
    
    public function OutlineSearch()
    {
        return $this->hasMany(SearchOutline::class);
    }
}

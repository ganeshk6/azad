<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImage;

class Day extends Model
{
    use HasImage;
    
    public function SubDay()
    {
        return $this->hasMany(SubDay::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImage;

class Month extends Model
{
    use HasImage;
    public function SubMonth()
    {
        return $this->hasMany(SubMonth::class);
    }
}

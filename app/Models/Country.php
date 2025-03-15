<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImage;

class Country extends Model
{
    use HasImage;
    public function SubCountry()
    {
        return $this->hasMany(SubCountry::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImage;

class ForeignCountry extends Model
{
    use HasImage;
    public function SubForeignCountry()
    {
        return $this->hasMany(SubForeignCountry::class);
    }
}

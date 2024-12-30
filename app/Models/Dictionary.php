<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dictionary extends Model
{
    public function subEntries()
    {
        return $this->hasMany(SubDictionary::class);
    }
}

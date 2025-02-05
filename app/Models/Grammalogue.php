<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImage;

class Grammalogue extends Model
{
    use HasImage;

    public function subEntries()
    {
        return $this->hasMany(SubGrammalogue::class);
    }
}

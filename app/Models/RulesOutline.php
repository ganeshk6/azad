<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImage;

class RulesOutline extends Model
{
    use HasImage;
    public function TypeRulesOutline()
    {
        return $this->hasMany(TypeRulesOutline::class);
    }
}

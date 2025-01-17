<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RulesOutline extends Model
{
    public function TypeRulesOutline()
    {
        return $this->hasMany(TypeRulesOutline::class);
    }
}

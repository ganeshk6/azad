<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImage;

class Notice extends Model
{
    use HasImage;

    public function SubNotice()
    {
        return $this->hasMany(SubNotice::class);
    }
    
}

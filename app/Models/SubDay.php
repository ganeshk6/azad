<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImage;

class SubDay extends Model
{
    use HasImage;
    protected $fillable = ['day_id', 'title', 'image', 'language_id'];

}

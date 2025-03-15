<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImage;

class SubMonth extends Model
{
    use HasImage;
    protected $fillable = ['month_id', 'title', 'image', 'language_id'];

}

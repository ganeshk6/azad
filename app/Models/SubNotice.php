<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImage;

class SubNotice extends Model
{
    use HasImage;
    protected $fillable = ['notice_id', 'title', 'image', 'language_id'];

}

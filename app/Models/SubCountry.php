<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImage;

class SubCountry extends Model
{
    use HasImage;
    protected $fillable = ['country_id', 'title', 'image', 'language_id'];

}

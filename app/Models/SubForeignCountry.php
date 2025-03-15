<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImage;

class SubForeignCountry extends Model
{
    use HasImage;
    protected $fillable = ['foreign_country_id', 'title', 'image', 'language_id'];

}

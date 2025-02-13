<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImage;

class Person extends Model
{
    use HasImage;

    Protected $table = "persons";
    
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImage;

class TypeRulesOutline extends Model
{
    use HasImage;
    
    protected $table = 'type_rules_outlines'; 

    protected $fillable = ['rules_outline_id', 'word', 'description', 'signature'];

}

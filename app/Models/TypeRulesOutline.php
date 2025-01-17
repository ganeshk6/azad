<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeRulesOutline extends Model
{
    protected $table = 'type_rules_outlines'; 

    protected $fillable = ['rules_outline_id', 'word', 'description', 'signature'];

}

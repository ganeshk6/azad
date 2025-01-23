<?php

namespace App\Traits;

trait HasImage
{
    public function getImageAttribute($value)
    {
        if ($value) {
            return asset('storage/' . $value);
        }

        return null;
    }

    public function getSignAttribute($value)
    {
        if ($value) {
            return asset('storage/' . $value);
        }

        return null;
    }
    public function getSignatureAttribute($value)
    {
        if ($value) {
            return asset('storage/' . $value);
        }

        return null;
    }
}

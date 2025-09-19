<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    public function catalogo()
    {
        return $this->belongsTo(\App\Models\Catalogo::class, 'catalogo_id');
    }

    public function location()
    {
        return $this->belongsTo(\App\Models\Location::class, 'location_id');
    }
}

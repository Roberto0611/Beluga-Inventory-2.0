<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    public function inventory()
    {
        return $this->hasMany(\App\Models\Inventory::class, 'location_id');
    }
}

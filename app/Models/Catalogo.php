<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Catalogo extends Model
{
    use SoftDeletes;
    protected $table = 'catalogo';
    public $timestamps = false;
    protected $primaryKey = 'ID';

    public function inventory()
    {
        return $this->hasMany(\App\Models\Inventory::class, 'catalogo_id', 'ID');
    }
}


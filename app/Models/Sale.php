<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    public function details(){
        return $this->hasMany(SaleDetail::class);
    }

    public function user_info(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function payments(){
        return $this->hasMany(Payment::class, 'sale_id');
    }
}

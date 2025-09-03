<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    public $timestamps = false;

    protected $fillable = ["unit_name"];
    public function inventories()
    {
        return $this->hasMany(Inventory::class, "unit_id", "id");
    }
}

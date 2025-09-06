<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = "inventory";

    protected $fillable = [
        "kode_inventory",
        "product_name",
        "type",
        "quality",
        "unit_id",
        "stock",
        "price",
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($inventory) {
            // Pastikan ID auto increment sudah diketahui setelah save
            // jadi kita buat kode sementara lalu update setelah save
        });

        static::created(function ($inventory) {
            $tahun = date("Y");
            $kode = "ITM-" . str_pad($inventory->id, 3, "0", STR_PAD_LEFT);

            $inventory->kode_inventory = $kode;
            $inventory->save();
        });
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, "unit_id");
    }
}

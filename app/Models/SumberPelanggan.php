<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SumberPelanggan extends Model
{
    public $timestamps = false;
    public $fillable = ["nama_sumber"];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $table = "invoice_items";

    protected $fillable = [
        "invoice_id",
        "inventory_id",
        "quantity",
        "price",
        "sub_total",
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}

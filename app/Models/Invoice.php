<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        "invoice_number",
        "customer_name",
        "source_id",
        "status_id",
        "issue_date",
        "due_date",
        "discount",
        "tax_enabled",
        "grand_total",
    ];

    public function boot()
    {
        parent::boot();

        static::created(function ($invoice) {
            $tahun = date("Y");
            $kode =
                "INV-" .
                $tahun .
                "-" .
                str_pad($invoice->id, 3, "0", STR_PAD_LEFT);
            $invoice->invoice_number = $kode;
            $invoice->save();
        });
    }

    public function status()
    {
        return $this->belongsTo(InvoiceStatus::class);
    }

    public function source()
    {
        return $this->belongsTo(SumberPelanggan::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}

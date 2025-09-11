<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = "invoices";

    protected $fillable = [
        "customer_name",
        "source",
        "status",
        "issue_date",
        "due_date",
        "discount",
        "down_payment",
        "tax_enabled",
        "grand_total",
    ];

    protected $appends = ["remaining_payment"];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $year = $invoice->created_at->format("Y");
                $invoice->invoice_number =
                    "INV-" .
                    $year .
                    "-" .
                    str_pad($invoice->id, 3, "0", STR_PAD_LEFT);
                $invoice->save();
            }
        });
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get remaining payment amount
     */
    public function getRemainingPaymentAttribute()
    {
        return $this->grand_total - $this->down_payment;
    }

    /**
     * Check if invoice is fully paid
     */
    public function isFullyPaid()
    {
        return $this->down_payment >= $this->grand_total;
    }

    /**
     * Check if invoice is partially paid
     */
    public function isPartiallyPaid()
    {
        return $this->down_payment > 0 &&
            $this->down_payment < $this->grand_total;
    }

    /**
     * Get payment percentage
     */
    public function getPaymentPercentage()
    {
        if ($this->grand_total == 0) {
            return 0;
        }
        return ($this->down_payment / $this->grand_total) * 100;
    }
}

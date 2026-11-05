<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receiving extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'receipt_date',
        'supplier_id',
        'material_id',
        'invoice_qty',
        'received_qty',
        'unit',
        'vehicle_number',
        'driver_name',
        'lot_no',
        'remarks',
        'status',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'invoice_qty'  => 'decimal:2',
        'received_qty' => 'decimal:2',
        'is_active'    => 'boolean',
        'status'       => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Status Constants
    |--------------------------------------------------------------------------
    | 0 = Pending
    | 1 = Approved
    | 2 = In Progress (sent to Acid Testing / BBSU)
    | 3 = Completed
    | 4 = Cancelled
    */
    const STATUS_PENDING     = 0;
    const STATUS_APPROVED    = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_COMPLETED   = 3;
    const STATUS_CANCELLED   = 4;

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            0 => 'Pending',
            1 => 'Approved',
            2 => 'In Progress',
            3 => 'Completed',
            4 => 'Cancelled',
            default => 'Unknown',
        };
    }

    protected $appends = ['status_label'];

    // ─── Relationships ─────────────────────────────────────────────

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->select('id', 'name');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->select('id', 'name');
    }
}

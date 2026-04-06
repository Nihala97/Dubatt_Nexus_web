<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcidTesting extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'acid_test_header';

    protected $fillable = [
        'test_date',
        'lot_number',
        'supplier_id',
        'vehicle_number',
        'avg_pallet_weight',
        'foreign_material_weight',
        'avg_pallet_and_foreign_weight',
        'invoice_qty',
        'received_qty',
        'net_avg_acid_pct',
        'status',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'test_date' => 'date',
        'avg_pallet_weight' => 'decimal:2',
        'foreign_material_weight' => 'decimal:2',
        'avg_pallet_and_foreign_weight' => 'decimal:3',
        'invoice_qty' => 'decimal:2',
        'received_qty' => 'decimal:2',
        'net_avg_acid_pct' => 'decimal:4',
        'is_active' => 'boolean',
        'status' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Status Constants
    | 0 = Pending
    | 1 = Approved
    | 2 = In Progress
    | 3 = Completed
    | 4 = Cancelled
    */
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CANCELLED = 4;

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
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

    public function details()
    {
        return $this->hasMany(AcidTestPercentageDetail::class, 'acid_test_id');
    }
    public function acidTest()
    {
        return $this->belongsTo(AcidTesting::class, 'acid_test_id');
    }

    public function receiving()
    {
        return $this->belongsTo(Receiving::class, 'lot_number', 'lot_no');
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
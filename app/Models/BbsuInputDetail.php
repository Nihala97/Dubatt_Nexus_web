<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BbsuInputDetail extends Model
{
    protected $table = 'bbsu_input_details';

    protected $fillable = [
        'bbsu_batch_id',
        'lot_no',
        'quantity',
        'acid_percentage',
        'material_breakdown',
        'status',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quantity'           => 'decimal:4',
        'acid_percentage'    => 'decimal:4',
        'material_breakdown' => 'array',
        'is_active'          => 'boolean',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(BbsuBatch::class, 'bbsu_batch_id');
    }
}

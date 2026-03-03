<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BbsuOutputMaterial extends Model
{
    protected $table = 'bbsu_output_materials';

    protected $fillable = [
        'bbsu_batch_id',
        'metallic_qty',
        'metallic_yield',
        'paste_qty',
        'paste_yield',
        'fines_qty',
        'fines_yield',
        'pp_chips_qty',
        'pp_chips_yield',
        'abs_chips_qty',
        'abs_chips_yield',
        'separator_qty',
        'separator_yield',
        'battery_plates_qty',
        'battery_plates_yield',
        'terminals_qty',
        'terminals_yield',
        'acid_qty',
        'acid_yield',
        'status',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'metallic_qty'        => 'decimal:4',
        'metallic_yield'      => 'decimal:4',
        'paste_qty'           => 'decimal:4',
        'paste_yield'         => 'decimal:4',
        'fines_qty'           => 'decimal:4',
        'fines_yield'         => 'decimal:4',
        'pp_chips_qty'        => 'decimal:4',
        'pp_chips_yield'      => 'decimal:4',
        'abs_chips_qty'       => 'decimal:4',
        'abs_chips_yield'     => 'decimal:4',
        'separator_qty'       => 'decimal:4',
        'separator_yield'     => 'decimal:4',
        'battery_plates_qty'  => 'decimal:4',
        'battery_plates_yield'=> 'decimal:4',
        'terminals_qty'       => 'decimal:4',
        'terminals_yield'     => 'decimal:4',
        'acid_qty'            => 'decimal:4',
        'acid_yield'          => 'decimal:4',
        'is_active'           => 'boolean',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(BbsuBatch::class, 'bbsu_batch_id');
    }
}

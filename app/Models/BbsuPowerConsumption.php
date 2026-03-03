<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BbsuPowerConsumption extends Model
{
    protected $table = 'bbsu_power_consumption';

    protected $fillable = [
        'bbsu_batch_id',
        'initial_power',
        'final_power',
        'total_power_consumption',
        'status',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'initial_power'           => 'decimal:4',
        'final_power'             => 'decimal:4',
        'total_power_consumption' => 'decimal:4',
        'is_active'               => 'boolean',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(BbsuBatch::class, 'bbsu_batch_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BbsuBatch extends Model
{
    protected $table = 'bbsu_batches';

    protected $fillable = [
        'batch_no',
        'start_time',
        'end_time',
        'doc_date',
        'category',
        'status',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_time'  => 'datetime',
        'end_time'    => 'datetime',
        'doc_date'    => 'date',
        'is_active'   => 'boolean',
    ];

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    public function inputDetails(): HasMany
    {
        return $this->hasMany(BbsuInputDetail::class, 'bbsu_batch_id');
    }

    public function outputMaterial(): HasOne
    {
        return $this->hasOne(BbsuOutputMaterial::class, 'bbsu_batch_id');
    }

    public function powerConsumption(): HasOne
    {
        return $this->hasOne(BbsuPowerConsumption::class, 'bbsu_batch_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

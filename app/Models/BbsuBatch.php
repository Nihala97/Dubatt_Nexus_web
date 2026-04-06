<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BbsuBatch extends Model
{
    use SoftDeletes;

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
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'doc_date' => 'date',
        'is_active' => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────

    public function inputDetails()
    {
        return $this->hasMany(BbsuInputDetail::class, 'bbsu_batch_id')
            ->where('is_active', true);
    }

    /**
     * Output materials — now HAS MANY (one row per material, 9 rows per batch).
     * Old code used hasOne/outputMaterial — update every reference to use
     * outputMaterials (plural).
     */
    public function outputMaterials()
    {
        return $this->hasMany(BbsuOutputMaterial::class, 'bbsu_batch_id')
            ->where('is_active', true);
    }

    /**
     * @deprecated  kept for backwards-compat during transition.
     * Returns the first output material row — use outputMaterials() instead.
     */
    public function outputMaterial()
    {
        return $this->hasMany(BbsuOutputMaterial::class, 'bbsu_batch_id')
            ->where('is_active', true);
    }

    public function powerConsumption()
    {
        return $this->hasOne(BbsuPowerConsumption::class, 'bbsu_batch_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
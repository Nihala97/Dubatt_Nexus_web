<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefiningBatch extends Model
{
    protected $table = 'refining_batches';

    protected $fillable = [
        'batch_no',
        'pot_no',
        'material_id',
        'date',
        'lpg_initial',
        'lpg_final',
        'lpg_consumption',
        'lpg2_initial',
        'lpg2_final',
        'lpg2_consumption',
        'electricity_initial',
        'electricity_final',
        'electricity_consumption',
        'oxygen_flow_nm3',
        'oxygen_flow_kg',
        'oxygen_flow_time',
        'oxygen_consumption',
        'total_process_time',
        'remarks',
        'status',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = ['date' => 'date', 'is_active' => 'boolean'];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
    public function rawMaterials(): HasMany
    {
        return $this->hasMany(RefiningRawMaterial::class, 'refining_batch_id');
    }
    public function chemicals(): HasMany
    {
        return $this->hasMany(RefiningChemical::class, 'refining_batch_id');
    }
    public function processDetails(): HasMany
    {
        return $this->hasMany(RefiningProcessDetail::class, 'refining_batch_id');
    }
    public function finishedGoodsBlocks(): HasMany
    {
        return $this->hasMany(RefiningFinishedGoodsBlock::class, 'refining_batch_id');
    }
    public function finishedGoodsSummary(): HasMany
    {
        return $this->hasMany(RefiningFinishedGoodsSummary::class, 'refining_batch_id');
    }
    public function drossBlocks(): HasMany
    {
        return $this->hasMany(RefiningDrossBlock::class, 'refining_batch_id');
    }
    public function drossSummary(): HasMany
    {
        return $this->hasMany(RefiningDrossSummary::class, 'refining_batch_id');
    }
}
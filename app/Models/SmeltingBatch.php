<?php
// ═══════════════════════════════════════════════════════════════════
// app/Models/SmeltingBatch.php
// ═══════════════════════════════════════════════════════════════════
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmeltingBatch extends Model
{
    use SoftDeletes;

    protected $table = 'smelting_batches';

    protected $fillable = [
        'batch_no',
        'charge_no',
        'rotary_no',
        'date',
        'start_time',
        'end_time',
        'lpg_consumption',
        'o2_consumption',
        'id_fan_initial',
        'id_fan_final',
        'id_fan_consumption',
        'rotary_power_initial',
        'rotary_power_final',
        'rotary_power_consumption',
        'output_material',
        'output_qty',
        'remarks',
        'status',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'lpg_consumption' => 'decimal:3',
        'o2_consumption' => 'decimal:3',
        'id_fan_initial' => 'decimal:3',
        'id_fan_final' => 'decimal:3',
        'id_fan_consumption' => 'decimal:3',
        'rotary_power_initial' => 'decimal:3',
        'rotary_power_final' => 'decimal:3',
        'rotary_power_consumption' => 'decimal:3',
        'output_qty' => 'decimal:3',
        'is_active' => 'boolean',
    ];

    public function rawMaterials(): HasMany
    {
        return $this->hasMany(SmeltingRawMaterial::class, 'smelting_batch_id');
    }

    public function fluxChemicals(): HasMany
    {
        return $this->hasMany(SmeltingFluxChemical::class, 'smelting_batch_id');
    }

    public function processDetails(): HasMany
    {
        return $this->hasMany(SmeltingProcessDetail::class, 'smelting_batch_id');
    }

    public function temperatureRecords(): HasMany
    {
        return $this->hasMany(SmeltingTemperatureRecord::class, 'smelting_batch_id');
    }

    public function outputBlocks(): HasMany
    {
        return $this->hasMany(SmeltingOutputBlock::class, 'smelting_batch_id');
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
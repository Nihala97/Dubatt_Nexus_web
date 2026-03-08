<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class RefiningRawMaterial extends Model
{
    protected $table = 'refining_raw_materials';
    protected $fillable = [
        'refining_batch_id',
        'raw_material_id',
        'qty',
        'smelting_batch_id',
        'smelting_batch_no',
        'is_active',
        'status',
        'created_by',
        'updated_by',
    ];
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'raw_material_id');
    }
}
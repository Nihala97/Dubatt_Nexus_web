<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefiningDrossSummary extends Model
{
    protected $table = 'refining_dross_summary';
    protected $fillable = [
        'refining_batch_id',
        'material_id',
        'total_qty',
        'is_active',
        'status',
        'created_by',
        'updated_by',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Material::class, 'material_id');
    }
}
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class RefiningChemical extends Model
{
    protected $table = 'refining_chemicals';
    protected $fillable = [
        'refining_batch_id',
        'chemical_id',
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
        return $this->belongsTo(Material::class, 'chemical_id');
    }
}
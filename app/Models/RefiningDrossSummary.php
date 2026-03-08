<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
}
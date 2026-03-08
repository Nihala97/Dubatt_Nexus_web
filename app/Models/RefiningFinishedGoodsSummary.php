<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class RefiningFinishedGoodsSummary extends Model
{
    protected $table = 'refining_finished_goods_summary';
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
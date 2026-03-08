<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class RefiningFinishedGoodsBlock extends Model
{
    protected $table = 'refining_finished_goods_blocks';
    protected $fillable = [
        'refining_batch_id',
        'material_id',
        'block_sl_no',
        'block_weight',
        'is_active',
        'status',
        'created_by',
        'updated_by',
    ];
}
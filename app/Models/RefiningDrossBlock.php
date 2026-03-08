<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class RefiningDrossBlock extends Model
{
    protected $table = 'refining_dross_blocks';
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
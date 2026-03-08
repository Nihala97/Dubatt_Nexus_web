<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class RefiningProcessDetail extends Model
{
    protected $table = 'refining_process_details';
    protected $fillable = [
        'refining_batch_id',
        'refining_process',
        'start_time',
        'end_time',
        'total_time',
        'is_active',
        'status',
        'created_by',
        'updated_by',
    ];
}
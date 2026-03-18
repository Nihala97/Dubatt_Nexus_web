<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materials';

    protected $fillable = [
        'material_code',
        'material_name',
        'secondary_name',
        'stock_code',
        'category',
        'section',
        'unit',
        'available_qty',
        'status',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'available_qty' => 'decimal:3',
        'is_active' => 'boolean',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

}
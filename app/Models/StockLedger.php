<?php
// ══════════════════════════════════════════════════════════════════
// FILE: app/Models/StockLedger.php
// ══════════════════════════════════════════════════════════════════
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLedger extends Model
{
    protected $table = 'stock_ledgers';
    protected $fillable = [
        'material_id',
        'process_type',
        'process_id',
        'doc_no',
        'in_qty',
        'out_qty',
        'balance_qty',
        'status',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'in_qty' => 'float',
        'out_qty' => 'float',
        'balance_qty' => 'float',
        'is_active' => 'boolean',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
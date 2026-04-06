<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * BbsuOutputMaterial
 *
 * One row per output material per batch.
 *
 * material_code in this table = material_code in the materials table.
 * The 9 codes are: 1007, 1008, 1019, 1005, 1023, 1006, 1055, 1057, 1267.
 *
 * All material names, stock codes, categories etc. come from the materials
 * table — nothing is duplicated here.
 *
 * MATERIAL_KEYS only stores the blade key slug (used for DOM IDs like
 * out_qty_{key}) so the blade can map material_code <-> input field.
 */
class BbsuOutputMaterial extends Model
{
    use SoftDeletes;

    protected $table = 'bbsu_output_materials';

    protected $fillable = [
        'bbsu_batch_id',
        'material_code',
        'qty',
        'yield_pct',
        'status',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'qty' => 'float',
        'yield_pct' => 'float',
        'is_active' => 'boolean',
    ];

    /**
     * Maps material_code => blade key slug.
     * Order defines display order in the output table.
     * Everything else (name, stock_code, category) is in the materials table.
     */
    public const MATERIAL_KEYS = [
        '1007' => 'metallic',
        '1008' => 'paste',
        '1019' => 'fines',
        '1005' => 'pp_chips',
        '1023' => 'abs_chips',
        '1006' => 'separator',
        '1055' => 'battery_plates',
        '1057' => 'terminals',
        '1267' => 'acid',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function batch()
    {
        return $this->belongsTo(BbsuBatch::class, 'bbsu_batch_id');
    }

    /**
     * Matching row in the materials table.
     * bbsu_output_materials.material_code = materials.material_code
     */
    public function material()
    {
        return $this->belongsTo(Material::class, 'material_code', 'material_code');
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    /** Returns the blade key slug, e.g. 'metallic', 'paste'. */
    public function getKeyAttribute(): ?string
    {
        return self::MATERIAL_KEYS[$this->material_code] ?? null;
    }
}
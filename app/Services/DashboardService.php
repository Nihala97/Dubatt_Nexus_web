<?php

namespace App\Services;

use App\Models\AcidTesting;
use App\Models\BbsuBatch;
use App\Models\Receiving;
use App\Models\RefiningBatch;
use App\Models\SmeltingBatch;
use Illuminate\Database\Eloquent\Model;

class DashboardService
{
    /**
     * Plant modules shown on the Flutter / mobile home dashboard.
     *
     * @var array<string, class-string<Model>>
     */
    private const MODULES = [
        'receiving' => Receiving::class,
        'acid_testing' => AcidTesting::class,
        'bbsu' => BbsuBatch::class,
        'smelting' => SmeltingBatch::class,
        'refining' => RefiningBatch::class,
    ];

    /**
     * Record counts for all plant modules (no role / permission filter).
     *
     * @return array<string, array{total: int, draft: int, submitted: int}>
     */
    public function summary(): array
    {
        $summary = [];

        foreach (self::MODULES as $key => $modelClass) {
            $summary[$key] = $this->countsFor($modelClass);
        }

        return $summary;
    }

    /**
     * @param class-string<Model> $modelClass
     * @return array{total: int, draft: int, submitted: int}
     */
    private function countsFor(string $modelClass): array
    {
        $base = $modelClass::query()->where('is_active', true);

        $draft = (clone $base)->where('status', 0)->count();
        $submitted = (clone $base)->where('status', '>=', 1)->count();

        return [
            'total' => $draft + $submitted,
            'draft' => $draft,
            'submitted' => $submitted,
        ];
    }
}

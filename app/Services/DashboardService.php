<?php

namespace App\Services;

use App\Models\AcidTesting;
use App\Models\BbsuBatch;
use App\Models\Receiving;
use App\Models\RefiningBatch;
use App\Models\SmeltingBatch;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DashboardService
{
    private const MODULES = [
        'receiving'    => ['slug' => 'receiving',    'model' => Receiving::class,     'date_column' => 'created_at'],
        'acid_testing' => ['slug' => 'acid_testing', 'model' => AcidTesting::class,   'date_column' => 'created_at'],
        'bbsu'         => ['slug' => 'bbsu',         'model' => BbsuBatch::class,      'date_column' => 'created_at'],
        'smelting'     => ['slug' => 'smelting',     'model' => SmeltingBatch::class,  'date_column' => 'created_at'],
        'refining'     => ['slug' => 'refining',     'model' => RefiningBatch::class,  'date_column' => 'created_at'],
    ];

    /**
     * Record counts per module, with optional date filters.
     *
     * @param  array{from?: string, to?: string, month?: string|int, year?: string|int} $filters
     * @return array<string, array{total: int, draft: int, submitted: int}>
     */
    public function summary(array $filters = []): array
    {
        $summary = [];

        foreach (self::MODULES as $key => $config) {
            $summary[$key] = $this->countsFor(
                $config['model'],
                $config['date_column'],
                $filters,
            );
        }

        return $summary;
    }

    /**
     * Counts for a single module model, respecting the active flag,
     * status split, and any date filters passed from the request.
     *
     * @param  class-string<Model>                                                      $modelClass
     * @param  string                                                                   $dateColumn
     * @param  array{from?: string, to?: string, month?: string|int, year?: string|int} $filters
     * @return array{total: int, draft: int, submitted: int}
     */
    private function countsFor(string $modelClass, string $dateColumn, array $filters): array
    {
        $base = $modelClass::query()
            ->where('is_active', true);

        $base = $this->applyDateFilters($base, $dateColumn, $filters);

        $draft     = (clone $base)->where('status', 0)->count();
        $submitted = (clone $base)->where('status', '>=', 1)->count();

        return [
            'total'     => $draft + $submitted,
            'draft'     => $draft,
            'submitted' => $submitted,
        ];
    }

    /**
     * Applies from / to / month / year constraints to a query builder.
     * Filters are additive — all provided constraints are ANDed together.
     *
     * Priority note:
     *   - `from` / `to` is an explicit date range and takes precedence.
     *   - `month` and `year` narrow within that range independently.
     *     e.g. year=2025 + month=5 → only May 2025 rows.
     *
     * @param  Builder                                                                   $query
     * @param  string                                                                   $column
     * @param  array{from?: string, to?: string, month?: string|int, year?: string|int} $filters
     */
    private function applyDateFilters(Builder $query, string $column, array $filters): Builder
    {
        // Explicit date range
        if (!empty($filters['from'])) {
            $query->whereDate($column, '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->whereDate($column, '<=', $filters['to']);
        }

        // Month (1–12)
        if (!empty($filters['month'])) {
            $query->whereMonth($column, (int) $filters['month']);
        }

        // Year
        if (!empty($filters['year'])) {
            $query->whereYear($column, (int) $filters['year']);
        }

        return $query;
    }
}
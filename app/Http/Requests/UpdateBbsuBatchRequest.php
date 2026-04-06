<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBbsuBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $batchId = $this->route('bbsu_batch');

        return [
            // ---- Header ----
            'batch_no'               => ['required', 'string', 'max:100', Rule::unique('bbsu_batches', 'batch_no')->ignore($batchId)],
            'start_time'             => 'required|date',    
            'end_time'               => 'required|date|after_or_equal:start_time',
            'doc_date'               => 'required|date',
            'category'               => 'required|string|max:100',

            // ---- Input Details (dynamic rows) ----
            'input_details'                        => 'required|array|min:1',
            'input_details.*.id'                   => 'nullable|integer|exists:bbsu_input_details,id',
            'input_details.*.lot_no'               => 'required|string|max:100',
            'input_details.*.quantity'             => 'required|numeric|min:0',
            'input_details.*.acid_percentage'      => 'required|numeric|min:0|max:100',
            'input_details.*.material_breakdown'   => 'nullable|array',

            // ---- Output Materials (nested by material code) ----
            'output_material'                      => 'required|array',
            'output_material.1007.qty'             => 'required|numeric|min:0', // LEAD GRIDS must have qty
            'output_material.*.qty'                => 'nullable|numeric|min:0',   // others are optional

            // ---- Power Consumption (single row) ----
            'power_consumption'                               => 'required|array',
            'power_consumption.initial_power'                 => 'required|numeric|min:0',
            'power_consumption.final_power'                   => 'required|numeric|min:0',
            'power_consumption.total_power_consumption'       => 'required|numeric|min:0',
        ];
    }
}

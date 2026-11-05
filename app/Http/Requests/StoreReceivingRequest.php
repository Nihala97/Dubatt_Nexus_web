<?php
// ── app/Http/Requests/StoreReceivingRequest.php ──────────────────

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Material;

class StoreReceivingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('driver_name')) {
            $this->merge([
                'driver_name' => is_string($this->driver_name) ? trim($this->driver_name) : $this->driver_name,
            ]);
        }
    }

    public function rules(): array
    {
        $materialId = $this->input('material_id');
        $isUlab = false;
        if ($materialId) {
            $material = Material::find($materialId);
            if ($material && strtoupper(trim($material->category ?? '')) === 'ULAB') {
                $isUlab = true;
            }
        }

        return [
            'receipt_date'   => 'required|date',
            'supplier_id'    => 'required|integer|exists:suppliers,id',
            'material_id'    => 'required|integer|exists:materials,id',
            'invoice_qty'    => 'required|numeric|min:0',
            'received_qty'   => 'required|numeric|min:0',
            'unit'           => 'required|string|max:50',
            'vehicle_number' => 'required|string|max:50',
            'driver_name'    => 'required|string|max:255',
            'lot_no'         => $isUlab
                ? 'nullable|string|max:100'
                : 'required|string|max:100|unique:receivings,lot_no',
            'remarks'        => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'driver_name.required' => 'Driver Name is required.',
        ];
    }
}
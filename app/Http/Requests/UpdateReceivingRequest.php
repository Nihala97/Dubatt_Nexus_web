<?php
// ── app/Http/Requests/UpdateReceivingRequest.php ─────────────────

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReceivingRequest extends FormRequest
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
        $id = $this->route('id');

        return [
            'receipt_date'   => 'sometimes|required|date',
            'supplier_id'    => 'sometimes|required|integer|exists:suppliers,id',
            'material_id'    => 'sometimes|required|integer|exists:materials,id',
            'invoice_qty'    => 'sometimes|required|numeric|min:0',
            'received_qty'   => 'sometimes|required|numeric|min:0',
            'unit'           => 'sometimes|required|string|max:50',
            'vehicle_number' => 'sometimes|required|string|max:50',
            'driver_name'    => 'required|string|max:255',
            'lot_no'         => ['sometimes', 'required', 'string', 'max:100', Rule::unique('receivings', 'lot_no')->ignore($id)],
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

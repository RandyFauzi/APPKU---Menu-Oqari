<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class SubmitPosOrderRequest extends HoldPosOrderRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'request_id' => ['required', 'uuid'],
            'recalled_order_id' => ['nullable', 'integer'],
            'payment_method' => ['required', 'in:CASH,QRIS,CARD,OTHER'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'fulfillment_type' => ['nullable', 'in:DINE_IN,TAKEAWAY'],
        ]);
    }
}

<?php

namespace App\Http\Requests\clientes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class CreditoStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'descripcion' => 'string|nullable|required_if:tipo,REVERSO',
            'monto' => [
                'required',
                'numeric',
                'min:0.01',
                'regex:/^\d+(\.\d{2})?$/',
                Rule::when(
                    $this->input('tipo') === 'REVERSO' && $this->input('credito_cliente.saldo') !== null,
                    [
                        'lte:credito_cliente.saldo',
                    ]
                ),
            ],
            'tipo' => 'required',
            'credito_cliente' => 'nullable',
        ];
    }
    
    public function messages(): array
    {
        return [
            "required"      => "El campo es requerido.",
            "monto.min"     => "El monto debe ser al menos :min.",
            "monto.numeric" => "El monto debe ser un número.",
            "monto.regex"   => "El monto debe ser de tipo numérico con un máximo de 2 decimales.",
            "monto.lte"     => "El monto no puede ser mayor al saldo disponible.",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        Log::error($validator->errors());
        throw new ValidationException($validator, response()->json([
            'ok' => false,
            'text' => 'Error de validación',
            'errors' => $validator->errors()
        ], 422));
    }
}
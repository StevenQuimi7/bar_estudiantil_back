<?php

namespace App\Http\Requests\ventas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;


class VentaStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_cliente'             => 'required',
            'detalles'               => 'required|array',
            'detalles.*.id_producto' => 'required|integer',
            'detalles.*.cantidad'    => 'required|integer|min:1',
            'detalles.*.precio'      => 'required',
            'detalles.*.subtotal'    => 'required',
        ];
    }
    public function messages()
    {
        return [
            "required" => "El campo :attribute es requerido.",
            "max"      => "El campo :attribute debe tener máximo :max caracteres.",
            "unique"   => "El campo :attribute ya se encuentra registrado.",
            "min"      => "El campo :attribute debe tener mínimo :min caracteres.",
            "integer"  => "El campo :attribute debe ser de tipo numerico.",
            "array"    => "El campo :attribute debe ser de tipo array.",
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        Log::error($validator->errors());
        throw new ValidationException($validator, response()->json([
            'ok' => false,
            'text' => 'Error validaciones',
            'errors' => $validator->errors()
        ], 422));
    }
}

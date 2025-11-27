<?php

namespace App\Http\Requests\productos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class ProductoStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'id_categoria'=> 'required',
            'precio'      => ['required','numeric','min:0.01','regex:/^\d+(\.\d{1,2})?$/'],
            'codigo'      => [
                'required',
                'min:1',
                Rule::unique('productos', 'codigo')->where(function ($query) {
                    return $query->where('activo', 1);
                }),
            ],
            'nombre'      => [
                'required',
                'min:2',
                Rule::unique('productos', 'nombre')->where(function ($query) {
                    return $query->where('activo', 1);
                }),
            ],
        ];
    }
    public function messages()
    {
        return [
            "required" => "El campo :attribute es requerido.",
            "max"      => "El campo :attribute debe tener máximo :max caracteres.",
            "unique"   => "El campo :attribute debe ser unico.",
            "numeric"  => "El campo :attribute debe ser de tipo numerico.",
            "min"      => "El campo :attribute debe tener mínimo :min caracteres.",
            "regex"    => "El campo :attribute debe ser de tipo numerico con 2 decimales.",
            // "string"   => "El campo :attribute debe ser de tipo string.",
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

<?php

namespace App\Http\Requests\especialidades;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class EspecialidadStoreRequest extends FormRequest
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
            'nombre' => [
                'required',
                'max:255',
                Rule::unique('especialidades', 'nombre')->where(function ($query) {
                    return $query->where('activo', 1)
                     ->where('nombre', mb_strtoupper($this->input('nombre'), 'UTF-8'));
                }),
            ],
        ];
    }
    public function messages()
    {
        return [
            "required" => "El campo :attribute es requerido.",
            "max"      => "El campo :attribute debe tener máximo :max caracteres.",
            "unique"   => "El campo :attribute ya se encuentra registrado.",
            "min"      => "El campo :attribute debe tener mínimo :min caracteres.",
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

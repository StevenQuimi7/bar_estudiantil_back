<?php

namespace App\Http\Requests\especialidades;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class EspecialidadUpdateRequest extends FormRequest
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
            'nombre' => [
                'required',
                'max:255',
                Rule::unique('especialidades', 'nombre')
                    ->ignore($this->route('id'))
                    ->where(fn ($query) => $query->where('activo', 1)),
            ],
        ];
    }

    public function messages(){
        return [
            "required" => "El campo :attribute es requerido.",
            "max"      => "El campo :attribute debe tener.maxcdn :max caracteres.",
            "min"      => "El campo :attribute debe tener:min :min caracteres.",
            "unique"   => "El campo :attribute ya existe."
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

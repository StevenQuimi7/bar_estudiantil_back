<?php

namespace App\Http\Requests\cursos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\nivel\Nivel;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class CursoStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id_nivel' => [
                'required',
                'integer',
                Rule::exists('niveles', 'id'),
            ],
            'id_grado' => [
                'required',
                'integer',
                Rule::exists('grados', 'id')
            ],
            'seccion' => 'required|string|min:1',
            'id_especialidad' => [
                'nullable',
                'integer',
                Rule::requiredIf(function () {
                    $nivel = Nivel::find($this->input('id_nivel'));
                    return $nivel && $nivel->nombre === 'BACHILLERATO';
                }),
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'required'                 => 'El campo :attribute es requerido.',
            'min'                      => 'El campo :attribute debe tener al menos :min caracteres.',
            'string'                   => 'El campo :attribute debe ser de tipo string.',
            'integer'                  => 'El campo :attribute debe ser un número entero.',
            'id_especialidad.required' => 'La especialidad es obligatoria para Bachillerato.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        Log::error('Validation failed for CursoStoreRequest', $validator->errors()->toArray());
        
        throw new ValidationException($validator, response()->json([
            'ok' => false,
            'text' => 'Error de validación',
            'errors' => $validator->errors(),
        ], 422));
    }
}

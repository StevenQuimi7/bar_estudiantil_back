<?php

namespace App\Http\Requests\grados;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class GradoUpdateRequest extends FormRequest
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
            'grado' => [
                'required',
                'integer',
                'max:15',
                'min:1',
                Rule::unique('grados', 'grado')
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
            "integer"  => "El campo :attribute debe ser de tipo numerico.",
            "unique"   => "El campo :attribute debe ser unico."
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

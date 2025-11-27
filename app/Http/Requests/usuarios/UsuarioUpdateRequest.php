<?php

namespace App\Http\Requests\usuarios;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class UsuarioUpdateRequest extends FormRequest
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
            'nombres'   => 'nullable|string|max:60',
            'apellidos' => 'nullable|string|max:60',
            'email' => [
                'required',
                'email',
                'max:60',
                Rule::unique('users', 'email')
                    ->ignore($this->route('id'))
                    ->where(fn ($query) => $query->where('activo', 1)),
            ],
        ];
    }

    public function messages(){
        return [
            "required"        => "El campo :attribute es requerido.",
            "max"             => "El campo :attribute debe tener máximo :max caracteres.",
            "integer"         => "El campo :attribute debe ser entero.",
            "unique"          => "El campo :attribute debe ser unico.",
            "email"           => "El campo :attribute debe ser de tipo email.",
            "string"          => "El campo :attribute debe ser de tipo string."
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

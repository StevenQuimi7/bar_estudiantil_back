<?php

namespace App\Http\Requests\auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthRequest extends FormRequest
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
            'nombres' => 'nullable|string|max:60',
            'apellidos' => 'nullable|string|max:60',
            'username' => 'required|string|max:60|unique:users,username',
            'email' => 'required|email|max:60|unique:users,email',
            'password' => 'required|min:6|max:255',
        ];
    }
    public function messages()
    {
        return [
            "required"        => "El campo :attribute es requerido.",
            "max"             => "El campo :attribute debe tener máximo :max caracteres.",
            "min"             => "El campo :attribute debe tener mínimo :min caracteres.",
            "numeric"         => "El campo :attribute debe ser numérico.",
            "date"            => "El campo :attribute debe ser una fecha.",
            "between"         => "El campo :attribute debe estar comprendido entre 0 y 100",
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

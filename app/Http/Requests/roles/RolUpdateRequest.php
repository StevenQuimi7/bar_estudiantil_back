<?php

namespace App\Http\Requests\roles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class RolUpdateRequest extends FormRequest
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
            'name' => [
                'required',
                'min:3',
                Rule::unique('roles', 'name')
                    ->ignore($this->route('id'))
                    ->where(fn ($query) => $query->where('activo', 1)),
            ],
        ];
    }

    public function messages(){
        return [
            "required" => "El campo nombre es requerido.",
            "min"      => "El campo :attribute debe tener minimo :min caracteres.",
            "unique"   => "El campo :attribute debe ser unico.",
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

<?php

namespace App\Http\Requests\clientes;

use App\Models\cliente\TipoCliente;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class ClienteStoreRequest extends FormRequest
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
            'id_tipo_cliente'       => 'required',
            'nombres'               => 'required|min:3',
            'apellidos'             => 'required|min:3',
            'numero_identificacion' => [
                'required',
                'max:10',
                'min:10',
                Rule::unique('clientes', 'numero_identificacion')->where(function ($query) {
                    return $query->where('activo', 1);
                }),
            ],
            'id_curso'              => [
                'nullable',
                'integer',
                Rule::requiredIf(function () {
                    $tipo = TipoCliente::find($this->input('id_tipo_cliente'));
                    return $tipo && $tipo->nombre === 'ESTUDIANTE';
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
            "min"      => "El campo :attribute debe tener mínimo :min caracteres.",
            "interger" => "El campo :attribute debe ser de tipo numerico.",
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
